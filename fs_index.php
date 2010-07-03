<?php
ini_set('display_errors','on');

/*
Plugin Name: FotoSlide
Plugin URI: http://www.kevinbradwick.co.uk/2010/06/wps3slider-plugin-for-wordpress/ 
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
	wp_register_script('intelislide', WP_PLUGIN_URL . '/wps3slider/assets/intelislide.jquery.min.js', array('jquery'), '1.0',false);
	wp_enqueue_script('intelislide');
}
add_action('init','fs_register_scripts');