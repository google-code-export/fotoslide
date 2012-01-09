<?php

/**
 * Gallery select page
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
<?php 
$total = $wpdb->get_row('SELECT COUNT(*) AS total FROM '.FS_TABLENAME)->total;

	require_once(dirname(__FILE__).'/fs_helpers.php');
	require_once(dirname(__FILE__).'/fs_paginator.php');
	
	$paginator = new FS_Paginator(array(
		'baseUrl'=>WP_PLUGIN_URL.'/fotoslide/fs_galselect.php',
		'pageLimit'=>5,
		'totalItems'=>$total
	));
	$offset = ($paginator->getCurrentPage() - 1) * 5;
	if( $offset < 0 ) { $offset = 0; }

	// get all galleries
	$sql = 'SELECT * FROM '.FS_TABLENAME.' LIMIT '. $offset . ',5';
	$items = $wpdb->get_results($sql);
	?>
	<div id="fs-medialist-wrap">
		<div class="tablenav">
			<div class="alignleft">
		    	<h3><?php _e('FotoSlide Galleries'); ?></h3>
		    	<p><?php _e('Copy and paste the code of the gallery into the editor to show it on your page');?></p>
		    </div>
			<div class="tablenav-pages"><?php echo $paginator->render(); ?></div>
		</div>
		<form method="post" action="">
		<table class="widefat">
		  <thead>
		    <tr>
		      <th scope="col" width="25px" class="manage-column"><?php _e('ID'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Gallery Name'); ?></th>
		      <th scope="col" class="manag-column"><?php _e('Image count'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Size (w x h)'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Timeout'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Transition Speed'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Code'); ?></th>
		    </tr>
		  </thead>
		  <tfoot>
		    <tr>
		      <th scope="col" width="25px" class="manage-column"><?php _e('ID'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Gallery Name'); ?></th>
		      <th scope="col" class="manag-column"><?php _e('Image count'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Size (w x h)'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Timeout'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Transition Speed'); ?></th>
		      <th scope="col" class="manage-column"><?php _e('Code'); ?></th>
		    </tr>
		  </tfoot>
		  <tbody>
		  <?php if($items && (count($items) > 0)) : ?>
		    
		    <?php foreach($items as $gallery) : ?>
		    <tr id="fs-<?php echo $gallery->id; ?>">
		      <td><?php echo $gallery->id; ?></td>
		      <td><?php echo stripslashes($gallery->gallery_name); ?></td>
		      <td><?php echo count(unserialize($gallery->items)); ?></td>
		      <td><?php echo $gallery->width . ' x ' . $gallery->height; ?></td>
		      <td><?php echo $gallery->timeout; ?></td>
		      <td><?php echo $gallery->transition_speed; ?></td>
		      <td><input type="text" class="code" value='<?php _e('[fs id="' . $gallery->id . '"]')?>' /></td>
		    </tr>
		    <?php endforeach; ?>
		    
		  <?php else : ?>
		    <tr>
		      <td colspan="7"><?php _e('You currently do not have any galleries'); ?></td>
		    </tr>
		  <?php endif; ?>
		  </tbody>
		</table>
		</form>
		<div class="tablenav">
			<div class="tablenav-pages"><?php echo isset($p) ? $p->show() : ''; ?></div>
		</div>
	</div>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function($) {
		$('input.code').click(function() {
			$(this).select();
		});
	});
	//]]>
	</script>
<?php

?>
</body>
</html>