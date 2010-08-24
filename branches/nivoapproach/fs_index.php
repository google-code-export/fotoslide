<?php
/*
Plugin Name: FotoSlide
Plugin URI: http://www.kevinbradwick.co.uk/2010/07/fotoslide-plugin-for-wordpress/ 
Description: A plugin that renders multiple slideshows on your site
Version: 1.0
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

// define url to plugin directory
defined('WP_PLUGIN_URL') || define(WP_PLUGIN_URL, WP_CONTENT_URL . '/plugins', true);

// define admin url
define('WP_PLUGIN_BASE_URL', get_bloginfo('url') . '/wp-admin/upload.php?page=fotoslide', true);

// define db table name
define('FS_TABLENAME', $wpdb->prefix.'fs_galleries', true);

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
 * @return	null
 */
function fs_activation()
{
	global $wpdb;
	$schema = str_replace('{tablename}', FS_TABLENAME, file_get_contents(dirname(__FILE__).'/assets/schema.sql'));
	$wpdb->query($schema);
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