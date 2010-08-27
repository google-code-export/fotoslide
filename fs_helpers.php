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
	$gallery = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.FS_GALTBL.' WHERE id = %d',array($galleryID)));
	$res = $wpdb->get_results($wpdb->prepare('SELECT id FROM '.FS_ITEMTBL.' WHERE gallery_id = %d',array($galleryID)));
	if(!$res)
		return false;
		
	$items = array();
	foreach($res as $row)
		$items[]=$row->id;
		
	$sql = "SELECT
			  m.meta_value,
			  i.caption_text,
			  i.href
			FROM $wpdb->postmeta m
			JOIN ".FS_ITEMTBL." i ON m.post_id=i.post_id
			WHERE i.id IN(".implode(',',$items).")
			AND m.meta_key = '_wp_attached_file'
			ORDER BY i.order_num ASC";
	
	$images = $wpdb->get_results($sql);
	if(!$images)
		return false;
	?>
	
	<!-- fotoslide #<?php echo $gallery->id; ?> -->
	<div id="fotoslide-<?php echo $gallery->id; ?>" class="<?php echo $gallery->class_attribute; ?>">
	<?php foreach($images as $image) : ?>
	  <img src="<?php echo getUploadPath().$image->meta_value; ?>" alt="Image" />
	<?php endforeach; ?>
	</div>
	
	<script type="text/javascript">
	//<[CDATA[
	jQuery(document).ready(function($) {
		$('#fotoslide-<?php echo $gallery->id; ?>').nivoSlider({
				controlNav:false,
				controlNavThumbs:false,
				directionNav:false,
				effect:"<?php echo $gallery->effect; ?>",
				captionOpacity:<?php echo $gallery->captionOpacity; ?>,
				animSpeed:<?php echo $gallery->animSpeed; ?>,
				pauseTime:<?php echo $gallery->pauseTime; ?>
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