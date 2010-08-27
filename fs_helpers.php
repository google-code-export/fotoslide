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
			  i.id,
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
	<div id="fotoslide-<?php echo $gallery->id; ?>">
	<?php 
	$captions = array();
	
	foreach($images as $image) {
		$ret = '';
		$title = '';
		if(!empty($image->caption_text)) {
			$title = 'title="#fs-caption-id-'.$image->id.'" ';
			$captions[] = array(
				'id'=>'fs-caption-id-'.$image->id,
				'content'=>$image->caption_text
			);
		}
		
		if(!empty($image->href))
			$ret .= '<a href="'.stripslashes($image->href).'">';
			
		$ret .= '<img '.$title.'src="'.WP_PLUGIN_URL.'/fotoslide/timthumb.php?src=';
		$ret .= getUploadPath().'/'.$image->meta_value;
		$ret .= '&w='.$gallery->width.'&h='.$gallery->height.'" alt="Image" />';
		if(!empty($image->href))
			$ret .= '</a>';
		
		echo $ret;
	}
	
	?>
	</div><!-- fotoslide #<?php echo $gallery->id; ?> -->
	<?php 
	// render captions
	foreach($captions as $caption) {
		$ret = '<div id="'.$caption['id'].'" class="'.$gallery->class_attribute.'">';
		$ret .= $caption['content'];
		$ret .= '</div>';
		echo $ret;
	}
	?>
	
	<script type="text/javascript">
	//<[CDATA[
	jQuery(document).ready(function($) {
		$('#fotoslide-<?php echo $gallery->id; ?>').css({
			width: '<?php echo $gallery->width; ?>px',
			height: '<?php echo $gallery->height; ?>px'
		}).nivoSlider({
				controlNav:false,
				controlNavThumbs:false,
				directionNav:false,
				effect:"<?php echo $gallery->effect; ?>",
				captionOpacity:<?php echo $gallery->captionOpacity; ?>,
				animSpeed:<?php echo $gallery->animSpeed; ?>,
				pauseTime:<?php echo $gallery->pauseTime; ?>,
				slices:<?php echo $gallery->slices; ?>,
				directionNav:<?php echo $gallery->directionNav == 1 ? 'true' : 'false'; ?>
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