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
 * @since	2.0
 * @param 	string $galleryID
 * @return	null
 */
function fs_render_slider( $galleryID )
{
	global $wpdb;
	
	$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($galleryID)));
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
	
	?>
	<div class="<?php echo $gallery->class_attribute; ?>" id="fotoslide-<?php echo $galleryID; ?>" style="height:420px;">
	<?php
	$count = 1;
	foreach($images as $image) {
		$meta = unserialize($image->meta);
		$attributes = 'src="';
		$attributes .= WP_PLUGIN_URL.'/fotoslide/timthumb.php?src='.$meta['file'];
		$attributes .= '&amp;w='.$gallery->width.'&amp;h='.$gallery->height.'"';
		$attributes .= ' alt="Image"';
		if(!empty($meta['caption_text']))
			$attributes .= ' title="#fotoslide-image-'.$gallery->id.'-'.$count.'"';
		
		if(!empty($meta['image_link']))
			echo "<a href='{$meta['image_link']}'><img {$attributes} /></a>";
		else
			echo "<img {$attributes} />";
			
		$count++;
	}
	
	?>
	</div>
	
	<?php $count = 1; foreach($images as $image) : $meta = unserialize($image->meta); if(!empty($meta['caption_text'])) : ?>
	<div id="fotoslide-image-<?php echo $gallery->id.'-'.$count; ?>" class="nivo-html-caption">
    	<?php echo stripslashes($meta['caption_text']); ?>
	</div>
	<?php endif; $count++; endforeach; ?>

	<script type="text/javascript">
	//<[CDATA[
	jQuery(document).ready(function($) {
		$('#fotoslide-<?php echo $galleryID; ?>').nivoSlider({
				controlNav:false,
				controlNavThumbs:false,
				directionNav:false,
				effect:"<?php echo $gallery->effect; ?>",
				captionOpacity:<?php echo number_format(($gallery->caption_opacity / 100),1); ?>,
				animSpeed:<?php echo $gallery->transition_speed; ?>,
				pauseTime:<?php echo $gallery->timeout; ?>
		});
	});
	//]]>
	</script>
	<?php
}


/**
 * Get upload path
 * 
 * @return	string
 */
function getUploadPath()
{
	$db_value = get_option('upload_path',true);
	if(empty($db_value))
		$path = '/wp-content/uploads/';
	else
		$path = substr($db_value,-1,1) == '/' ? $db_value : $db_value . '/';
	
	return $path;
}

?>