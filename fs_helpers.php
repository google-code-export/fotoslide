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
function fs_render_slider( $gid )
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
	
	$style = 'width:'.$gallery->width.'px; height:'.$gallery->height.'px; overflow:hidden; display:block';
	
	$ret = "\t\n\n<span id=\"fotoslide-{$gid}\" class=\"fotoslide\" style=\"{$style}\">\n";
	
	// loop through
	foreach($images as $image) {
		$meta = unserialize($image->meta);

		if( !empty( $meta['image_link'] ) ) { 
			$ret .= '<a href="'.$meta['image_link'].'" class="intelslide"';
		}
		else {
			$ret .= '<span class="intelslide"';
		}
		
		if(empty($meta['caption_text'])) {
			$ret .= '>';
		} else {
			$ret .= ' title="'.stripslashes($meta['caption_text']).'" rel="';
			
			// set caption background colour
			if(!empty($meta['caption_bg_colour']))
				$ret .= 'bg:'.$meta['caption_bg_colour'].';';
			
			// set caption text colour
			if(!empty($meta['caption_text_colour']))
				$ret .= 'txt:'.$meta['caption_text_colour'].';';
			
			// set caption opacity
			if(!empty($meta['caption_opacity']))
				$ret .= 'opacity:'.$meta['caption_opacity'].';';
			
			// set caption position
			if(!empty($meta['caption_location']))
				$ret .= 'pos:'.$meta['caption_location'].';';
				
			$ret .= '">';
		}
		
		$ret .= '<img src="'.WP_PLUGIN_URL.'/fotoslide/timthumb.php?src='.$meta['file'].'&amp;w='.$gallery->width.'&amp;h='.$gallery->height.'"';
		$ret .= ' alt="Image" />';

		if( !empty( $meta['image_link'] ) ) { 
			$ret .= '</a>'."\n";
		}
		else {
			$ret .= '</span>'."\n";
		}
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