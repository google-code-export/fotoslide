<?php
/*
Plugin Name: FotoSlide
Plugin URI: http://www.kevinbradwick.co.uk/2010/07/fotoslide-plugin-for-wordpress/ 
Description: A plugin that renders multiple slideshows on your site
Version: 2.0
Author: Kevin Bradwick <kbradwick@gmail.com>
Author URI: http://www.kevinbradwick.co.uk
Licence: GPL2

Copyright 2010 Kevin Bradwick <kbradwick@gmail.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

global $wpdb;
ini_set('display_errors','on');

// define url to plugin directory
defined('WP_PLUGIN_URL') || define(WP_PLUGIN_URL, WP_CONTENT_URL . '/plugins', true);
define('WP_PLUGIN_BASE_URL', get_bloginfo('url') . '/wp-admin/upload.php?page=fotoslide', true);
define('FS_GALTBL',$wpdb->prefix.'fotoslide_galleries',true);
define('FS_ITEMTBL',$wpdb->prefix.'fotoslide_items',true);

// load the helpers
require_once(dirname(__FILE__).'/fs_helpers.php');

// load admin menu
require_once(dirname(__FILE__).'/fs_admin.php');

// array holder for galleries
$pageGalleries = array();


/**
 * Register plugin activation
 *
 * Upon registering, the plugin will check for the exsitence of
 * the necessary table and create it if they do not exist
 * 
 * @todo new routine needed to test table schema and alter/create
 * as necessary
 *
 * @return	null
 */
function fs_activation()
{
	global $wpdb;
	
	list($gallery_schema, $item_schema) = explode('@next',file_get_contents(dirname(__FILE__).'/assets/schema.sql'));
	
	$wpdb->query(str_replace('{tablename_galleries}',FS_GALTBL,$gallery_schema));
	$wpdb->query(str_replace('{tablename_items}',FS_ITEMTBL,$item_schema));
	
	// check for old schema
	$tables = array();
	$tbl = 'Tables_in_'.DB_NAME;
	foreach($wpdb->get_results('SHOW TABLES') as $row)
		$tables[] = $row->$tbl;
	
	// migrate data if upgrading from 1.0+
	if(in_array($wpdb->prefix.'fs_galleries',$tables)) {
		$sql = 'INSERT INTO '.FS_ITEMTBL. ' (post_id,caption_text,href,gallery_id,order_num) VALUES ';
		foreach($wpdb->get_results("SELECT * FROM {$wpdb->prefix}fs_galleries") as $row) {
			// mirror old galleries into new table
			$wpdb->insert(FS_GALTBL,array(
				'id'=>$row->id,
				'gallery_name'=>$row->gallery_name,
				'dstamp'=>$row->dstamp,
				'width'=>$row->width,
				'height'=>$row->height,
				'pauseTime'=>$row->timeout,
				'animSpeed'=>$row->transition_speed
				),array('%d','%s','%s','%d','%d','%d','%d')
			);
			
			// insert gallery items
			if(count(unserialize($row->items)) > 0) {
				$postmeta_sql = "SELECT * FROM $wpdb->postmeta WHERE post_id IN(".implode(',',unserialize($row->items)).") AND meta_key IN('_fs_image_meta','_fs_image_order') ";
				$items = $wpdb->get_results($postmeta_sql);
				if($items) {
					$postmeta = array();
					foreach($items as $item)
						$postmeta[$item->post_id][$item->meta_key] = ($item->meta_key == '_fs_image_meta') ? unserialize($item->meta_value) : $item->meta_value;
					
					foreach($postmeta as $post_id => $meta) {
						$pid = (int)$post_id;
						$caption_text = isset($meta['_fs_image_meta']['caption_text']) ? $meta['_fs_image_meta']['caption_text'] : '';
						$href = isset($meta['_fs_image_meta']['image_link']) ? $meta['_fs_image_meta']['image_link'] : '';
						$gallery_id = $row->id;
						$order_num = isset($meta['_fs_image_order']) ? (int)$meta['_fs_image_order'] : 0;
						$sql .= "($pid,'".addslashes($caption_text)."','".addslashes($href)."',$gallery_id,$order_num),";
					}
				}
			}
		}
		
		if(substr($sql,-1)===',')
			$wpdb->query(substr($sql,0,-1));
	}
	
	
}
register_activation_hook(__FILE__, 'fs_activation');


/**
 * Regiser scripts
 *
 * This will register the javascripts required
 * for the plugin to work. All scripts are placed in
 * the footer for better performance, so make sure
 * the template function wp_footer() is used in your
 * theme files.
 *
 * @return null
 */
function fs_register_scripts()
{
	wp_register_script('nivoslider', WP_PLUGIN_URL.'/fotoslide/assets/nivoslider/jquery.nivo.slider.pack.js',array('jquery'));
	wp_register_style('nivosliderstyle',WP_PLUGIN_URL.'/fotoslide/assets/nivoslider/nivo-slider.css');
	wp_enqueue_script('nivoslider');
	wp_enqueue_style('nivosliderstyle');
}
add_action('init','fs_register_scripts');

/**
 * Register shortcode
 *
 * This adds the shortcode fs to the system so that
 * you can use the fs shortcode tag in the theme
 * files.
 *
 * @param	array
 * @return	null
 */
function fs_shortcode( $params )
{
	if(isset($params['id'])) {
		global $pageGalleries;
		$pageGalleries[] = $params['id'];
		echo fs_render_slider($params['id']);
	}
}
add_shortcode('fs','fs_shortcode');


/**
 * Render galleries
 *
 * This filters a post/page content for the shortcode and
 * renders the appropriate gallery on the page
 *
 * @param	string
 * @return	null
 */
function fs_filter_content( $content )
{
	global $pageGalleries;
	$pattern = '/\[fs\sid=\"([0-9]*)\"\]/i';
	$galleries = array();
	
	if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
		foreach($matches as $gallery) {
			$slider = fs_render_slider(trim($gallery[1]));
			if($slider) {
				$content = str_replace('[fs id="'.$gallery[1].'"]', $slider, $content);
				$galleries[] = (int)$gallery[1];
			} else {
				$content = preg_replace('/\[fs\sid=\"'.$gallery[1].'\"\]/', '', $content);
			}
		}
	}
	
	if(count($galleries) > 0)
		$pageGalleries = $galleries;
		
	return $content;
}
add_filter('the_content','fs_filter_content');


/**
 * Prepeare galleries code into the footer
 */
function fs_prepare_js( $galleries = array() )
{
	global $pageGalleries, $wpdb;
	
	if(count($pageGalleries) > 0) {
		$galleries = $wpdb->get_results("SELECT * FROM ".FS_TABLENAME." WHERE id IN(".implode(',',$pageGalleries).")");
		?>
		<!-- simpleslide -->
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function($) {
			<?php foreach($galleries as $gallery) : ?>
			
			$("#fotoslide-<?php echo $gallery->id; ?>").intelislide({width:<?php echo $gallery->width; ?>,height:<?php echo $gallery->height; ?>,tagType:'span',transitionSpeed:<?php echo $gallery->transition_speed; ?>,timeout:<?php echo $gallery->timeout; ?>});
			<?php endforeach; ?>
			
		});
		//]]>
		</script>
		<?php
	}
}
add_action('wp_footer','fs_prepare_js');


/**
 * Add icon to the page/post editor for selecting and inserting
 * FotoSlide galleries
 * 
 * @return	null
 */
function fs_media_buttons()
{
	?>
	<a class="thickbox" href="<?php echo WP_PLUGIN_URL; ?>/fotoslide/fs_galselect.php?TB_iframe=1&amp;height=400" title="<?php _e('Select a FotoSlide gallery'); ?>">
	  <img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/photos.png" alt="FotoSlide Gallery Selector" />
	</a>
	<?php
}
add_action('media_buttons','fs_media_buttons',11);
?>