<?php

/**
 * This wil render all images in the media library
 */

if(!file_exists(dirname(__FILE__).'/../../../wp-load.php')) {
	die('Could not locate wp-load.php');
}

require_once(dirname(__FILE__).'/../../../wp-load.php');
require_once(dirname(__FILE__).'/fs_index.php');

global $wpdb;

if (function_exists('admin_url')) {
	wp_admin_css_color('classic', __('Blue'), admin_url("css/colors-classic.css"), array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), admin_url("css/colors-fresh.css"), array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
} else {
	wp_admin_css_color('classic', __('Blue'), get_bloginfo('wpurl').'/wp-admin/css/colors-classic.css', array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), get_bloginfo('wpurl').'/wp-admin/css/colors-fresh.css', array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
}

wp_enqueue_script( 'common' );
wp_enqueue_script( 'jquery-color' );

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!current_user_can('manage_options'))
	wp_die(__('You do not have permission to view this page.'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
	<?php
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );
		wp_enqueue_style( 'media' );
	?>
    
    <script type="text/javascript">
	//<![CDATA[
		function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
	//]]>
	</script>
	<?php
	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
	if ( isset($content_func) && is_string($content_func) )
		do_action( "admin_head_{$content_func}" );
	?>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/plugins/fotoslide/assets/admin.css" media="screen" />
</head>
<body>

<div id="fs-medialist-wrap">
<h3><?php _e('Add a gallery item'); ?></h3>
<?php

// do pagination
$filter = '';
if(isset($_POST['filter']) || isset($_GET['filter'])) {
	$filter = isset($_POST['filter']) ? esc_attr($_POST['filter']) : esc_attr($_GET['filter']);
}

$sql = "SELECT COUNT(*) AS total FROM $wpdb->posts WHERE post_title LIKE '%{$filter}%' AND post_type='attachment' AND post_mime_type LIKE 'image%' ORDER BY post_date DESC";
if(isset($_GET['gid'])) {
	$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($_GET['gid'])));
	if($gallery && (count(unserialize($gallery->items)) > 0)) {
		$sql = str_replace('ORDER BY post_date DESC', " AND ID NOT IN(".implode(',',unserialize($gallery->items)).")", $sql).' ORDER BY post_date DESC';
	}
}

require_once(dirname(__FILE__).'/fs_helpers.php');
require_once 'fs_paginator.php';

$totalItems = $wpdb->get_row($sql);
$paginator = new FS_Paginator(array(
	'totalItems'=>$totalItems->total,
	'baseUrl'=>WP_PLUGIN_URL.'/fotoslide/fs_medialibrary.php',
	'pageLimit'=>5
));
$offset = ($paginator->getCurrentPage() * 5) - 5;
// get all image attachements
$items = $wpdb->get_results(str_replace('COUNT(*) AS total','*',$sql).' LIMIT '.$offset.',5');

// get all meta info in one transaction
if($items) {
	$ids = array();
	foreach($items as $item)
		$ids[] = $item->ID;
	
	$postMeta = array();
	$meta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id IN(" . implode(',',$ids) . ")");
	foreach($meta as $meta) {
		if(!isset($postMeta[$meta->post_id]))
			$postMeta[$meta->post_id] = array();
		
		if(in_array($meta->meta_key,array('_wp_attached_file','_wp_attachment_metadata'))) {
			$postMeta[$meta->post_id][$meta->meta_key] = ($meta->meta_key == '_wp_attachment_metadata') ? unserialize($meta->meta_value) : $meta->meta_value;
		}
	}
	?>
	<div class="alignright">
	<form method="post">
	  <input type="text" name="filter" id="filter" />
	  <input type="submit" value="Search" class="button-secondary" />
	</form>
	</div>
	
	<div class="tablenav">
		<div class="alignleft">
	    	<h3><?php _e('Image List'); ?></h3>
	    </div>
		<div class="tablenav-pages">
		<?php $paginator->render(); ?>
		</div>
	</div>
	<table class="widefat fixed" cellspacing="0">
	  <thead>
	    <tr>
	      <th scope="col"><?php _e('File'); ?></th>
          <th scope="col"><?php _e('Title'); ?></th>
          <th scope="col"><?php _e('Size'); ?></th>
          <th scope="col"><?php _e('Add'); ?></th>
	    </tr>
	  </thead>
	  <tfoot>
	    <tr>
	      <th scope="col"><?php _e('File'); ?></th>
          <th scope="col"><?php _e('Title'); ?></th>
          <th scope="col"><?php _e('Size'); ?></th>
          <th scope="col"><?php _e('Add'); ?></th>
	    </tr>
	  </tfoot>
	  <tbody>
	    <?php $class = ''; foreach($items as $post) : $class = 'alternate' == $class ? '' : 'alternate'; ?>
	    <tr class="<?php echo $class; ?>">
	      <td id="attachment-<?php echo $post->ID; ?>"><?php echo wp_get_attachment_image($post->ID,array(70,70)); ?></td>
          <td><?php echo esc_html($post->post_title); ?></td>
          <td><?php _e($postMeta[$post->ID]['_wp_attachment_metadata']['width'].' x '.$postMeta[$post->ID]['_wp_attachment_metadata']['height']); ?></td>
          <td><a href="#" class="button-secondary insert-button" id="media-<?php echo $post->ID; ?>"><?php _e('Insert'); ?></a></td>
	    </tr>
	    <?php endforeach; ?>
	  </tbody>
	</table>
	<?php
} else {
	?>
	<!-- no images stored -->
	<p><?php _e('You currently do not have any images stored in your media library'); ?></p>
	<?php
}
?>

<div class="tablenav">
	<div class="alignleft">
		<a href="#" rel='tb-close' class="button-secondary"><?php _e('Close'); ?></a>
	</div>
</div>

</div>


<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($)
{
	$('a.insert-button').click(function() {
		var _id = $(this).attr('id').substr(6);
		var _parent = window.parent.document;
		var _src = $('td#attachment-' + _id + ' img').attr('src');
		$('#image_post_id', window.parent.document).attr('value',_id);
		$('#image-select', _parent).hide();
		$('#image-preview td.item-details', _parent).html('<img src="' + _src + '" alt="Image" />');
		$('#image-preview', _parent).show();
		window.top.tb_remove();
	});

	$('a[rel=tb-close]').click(function() { window.top.tb_remove(); return false; });
});
//]]>
</script>
</body>
</html>