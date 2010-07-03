<?php

/**
 * Determine if this is a WPMU install
 *
 * @return boolean
 */
function is_wpmu()
{
	return function_exists('wpmu_create_user');
}


/**
 * Render the slider
 *
 * @param	integer
 * @return	mixed
 */
function wps3_render_slider( $gid )
{
	global $wpdb;
	
	$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($gid)));
	if(!$gallery)
		return false;
	
	if(count(unserialize($gallery->items)) < 1)
		return false;
	
	$uploadPath = get_option('upload_path');
	
	$sql = "SELECT
			  p.post_id,
			  p.meta_value,
			  (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = p.post_id AND meta_key = '_fs_image_meta') AS meta,
			  (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = p.post_id AND meta_key = '_wp_attached_file') AS image
			FROM $wpdb->postmeta p
			WHERE p.post_id IN(".implode(',',unserialize($gallery->items)).") AND p.meta_key = '_fs_image_order'
			ORDER BY p.meta_value ASC";
	
	$images = $wpdb->get_results($sql);
	
	if(!$images)
		return false;
	

	$ret = "\t\n\n<span id=\"intelislide-{$gid}\">\n";
	
	// loop through
	foreach($images as $image) {
		$meta = unserialize($image->meta);
		
		$ret .= '<a href="'.$meta['image_link'].'"';
		
		if(empty($meta['image_text'])) {
			$ret .= '>';
		} else {
			$ret .= ' title="'.stripslashes($meta['image_text']).'" rel="';
			
			// set caption background colour
			if(!empty($meta['span_bg_colour']))
				$ret .= 'bg:'.$meta['span_bg_colour'].';';
			
			// set caption text colour
			if(!empty($meta['span_text_colour']))
				$ret .= 'txt:'.$meta['span_text_colour'].';';
			
			// set caption opacity
			if(!empty($meta['span_opacity']))
				$ret .= 'opacity:'.$meta['span_opacity'].';';
			
			// set caption position
			if(!empty($meta['span_location']))
				$ret .= 'pos:'.$meta['span_location'].';';
				
			$ret .= '">';
		}
		
		$ret .= '<img src="'.WP_PLUGIN_URL.'/wps3slider/timthumb.php?src='.$meta['file'].'&amp;w='.$gallery->width.'&amp;h='.$gallery->height.'"';
		$ret .= ' alt="Image" /></a>'."\n";
	}
	
	$ret .= '<span class="caption"></span>';
	$ret .= "</span>\n\n";
	return $ret;
}


/**
 * Get upload path
 * 
 * @return	string
 */
function getUploadPath()
{
	$db_value = get_option('upload_path',true);
	if(empty($db_value)) {
		$path = '/wp-content/uploads/';
	} else {
		$path = substr($db_value,-1,1) == '/' ? $db_value : $db_value . '/';
	}
	return $path;
}

?>