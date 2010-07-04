<?php

/**
 * This file renders the galleries tab contents
 */

global $wpdb;

// include the form processor
require_once(dirname(__FILE__).'/fs_processform.php');

// gallery pagination and query
$galleryCount = (int)$wpdb->get_row("SELECT COUNT(*) as tot FROM ".FS_TABLENAME)->tot;
if($galleryCount > 0) {
	require_once(dirname(__FILE__).'/pagination.class.php');
	$p = new pagination;
	$currentPage = isset($_GET['paging']) ? $_GET['paging'] : 1;
	$p->items($galleryCount);
	$p->limit(5);
	$p->target('upload.php?page=wps3slider');
	$p->currentPage($currentPage);
	$p->parameterName('paging');
	$p->adjacents(1);
	$p->page = isset($_GET['paging']) ? (int)$_GET['paging'] : 1;
	$limit = "LIMIT " . ($p->page - 1) * $p->limit . ', ' . $p->limit;
}?>


<?php if($message['output']) : ?>
<div class="message <?php echo $message['type']; ?>"><?php _e($message['message']); ?></div>
<div class="clear"><br class="clear" /></div>
<?php endif; ?>


<!-- List all our galleries here @package FotoSlide -->
<div class="tablenav">
	<div class="alignleft">
    	<h3><?php _e('Gallery List'); ?></h3>
    </div>
	<div class="tablenav-pages"><?php echo ($galleryCount) > 0 ? $p->show() : ''; ?></div>
</div>
<table class="widefat fixed" cellspacing="0">
  <thead>
    <tr>
      <th scope="col" width="25px" class="manage-column"><?php _e('ID'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Gallery Name'); ?></th>
      <th scope="col" class="manag-column"><?php _e('Image count'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Size (w x h)'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Timeout'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Transition Speed'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Actions'); ?></th>
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
      <th scope="col" class="manage-column"><?php _e('Actions'); ?></th>
    </tr>
  </tfoot>
  <tbody class="list:post">
  <?php if($galleryCount > 0) : ?>
  
  	<!-- start gallery loop -->
    <?php $alt = false; foreach($wpdb->get_results("SELECT * FROM " .FS_TABLENAME.' '.$limit) as $gallery) : ?>
    <tr id="fs-gallery-<?php echo $gallery->id; ?>" valign="top"<?php if($alt) : ?> class="alternate"<?php endif; $alt = $alt ? false : true; ?>>
      <td><?php echo $gallery->id; ?></td>
      <td><?php echo stripslashes($gallery->gallery_name); ?></td>
      <td><?php echo count(unserialize($gallery->items)); ?></td>
      <td><?php echo $gallery->width . ' x ' . $gallery->height; ?></td>
      <td><?php echo $gallery->timeout; ?></td>
      <td><?php echo $gallery->transition_speed; ?></td>
      <td>
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=edit-gallery&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/wps3slider/assets/page_white_edit.png" alt="Edit" /></a> &nbsp; 
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=delete-gallery&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/wps3slider/assets/page_white_delete.png" alt="Delete" /></a> &nbsp;
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/wps3slider/assets/photos.png" alt="Items" /></a>
      </td>
    </tr>
    <?php endforeach; ?>
    <!-- end gallery loop -->
    
  <?php else : ?>
    <tr>
      <td colspan="7" class="empty-table-data"><p><?php _e('You currently do not have any galleries'); ?></p>
      <p><a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=new-gallery" class="button-secondary"><?php _e('New Gallery'); ?></a></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
<div class="tablenav">
    <div class="alignleft actions">
        <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=new-gallery" class="button-secondary"><?php _e('New Gallery'); ?></a>
    </div>
	<div class="tablenav-pages"><?php echo ($galleryCount) > 0 ? $p->show() : ''; ?></div>
</div>
<div class="clear"></div><br class="clear" />
<!-- END gallery listing @package FotoSlide -->



<?php if(isset($_GET['action']) && $_GET['action'] == 'new-gallery') : ?>
<?php
/**
 * GENERATE A NEW GALLERY FORM
 */
?>
<p>&nbsp;</p>
<hr />
<h3><?php _e('New Gallery'); ?></h3>
<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=new-gallery&amp;insert">
<?php wp_nonce_field('new-gallery'); ?>
<table class="form-table" id="fs-gallery-list">
  <tr valign="top">
    <th scope="row"><label for="gallery_name"><?php _e('Gallery Name'); ?></label></th>
    <td><input type="text" name="gallery_name" id="gallery_name" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_width"><?php _e('Width'); ?></label></th>
    <td><input type="text" name="gallery_width" id="gallery_width" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_height"><?php _e('Height'); ?></label></th>
    <td><input type="text" name="gallery_height" id="gallery_height" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_timeout"><?php _e('Timeout'); ?></label></th>
    <td><input type="text" name="gallery_timeout" id="gallery_timeout" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_speed"><?php _e('Transition Speed'); ?></label></th>
    <td><input type="text" name="gallery_transition_speed" id="gallery_transition_speed" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row">&nbsp;</th>
    <td><input type="submit" class="button-primary" value="<?php _e('Save'); ?>" /></td>
  </tr>
</table>
</form>




<?php elseif(isset($_GET['action']) && $_GET['action'] == 'edit-gallery' && isset($_GET['gid'])) : ?>
<?php
/**
 * GENERATE AN EDIT GALLERY FORM
 */
$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d", array($_GET['gid'])));
?>
<p>&nbsp;</p>
<hr />
<h3><?php _e('Edit Gallery'); ?></h3>
<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=edit-gallery&amp;gid=<?php echo $gallery->id; ?>&amp;update=">
<?php wp_nonce_field('edit-gallery'); ?>
<table class="form-table">
  <tr valign="top">
    <th scope="row"><label for="gallery_name"><?php _e('Gallery Name'); ?></label></th>
    <td><input type="text" name="gallery_name" id="gallery_name" class="regular-text" value="<?php echo stripslashes($gallery->gallery_name); ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_width"><?php _e('Width'); ?></label></th>
    <td><input type="text" name="gallery_width" id="gallery_width" class="regular-text" value="<?php echo $gallery->width; ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_height"><?php _e('Height'); ?></label></th>
    <td><input type="text" name="gallery_height" id="gallery_height" class="regular-text" value="<?php echo $gallery->height; ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_timeout"><?php _e('Timeout'); ?></label></th>
    <td><input type="text" name="gallery_timeout" id="gallery_timeout" class="regular-text" value="<?php echo $gallery->timeout; ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_speed"><?php _e('Transition Speed'); ?></label></th>
    <td><input type="text" name="gallery_transition_speed" id="gallery_transition_speed" class="regular-text" value="<?php echo $gallery->transition_speed; ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row">&nbsp;</th>
    <td><input type="submit" class="button-primary" value="<?php _e('Save'); ?>" /></td>
  </tr>
</table>
</form>



<?php elseif(isset($_GET['action']) && $_GET['action'] == 'delete-gallery') : ?>
<?php
/**
 * GENERATE A DELETE GALLERY FORM
 */
$confirmUrl = WP_PLUGIN_BASE_URL . '&amp;action=delete-gallery&amp;gid=' . $_GET['gid'] . '&amp;_wpnonce=' . wp_create_nonce('delete-gallery') . '&amp;confirm';
if($message['action'] == 'delete-gallery' && $message['showform'] == true) : ?>
<p>&nbsp;</p>
<hr />
<h3>Delete Gallery</h3>
<p>Are you sure you want to delete this gallery? <em>Note: This will not delete the items - they will remain in your Media Library</em></p>
<br />
<a href="<?php echo $confirmUrl; ?>" class="button-secondary">Yes, delete the gallery</a>
<?php endif; ?>


<?php elseif(isset($_GET['action']) && $_GET['action'] == 'gallery-items' && isset($_GET['gid'])) : ?>
<?php
/**
 * SHOW GALLERY ITEMS
 */
$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d", array($_GET['gid'])));
$items = array();

if($gallery) {
	$items = unserialize($gallery->items);
}

if(count($items) > 0) {
	$images = array();
	$in = implode(',',$items);
	$sql = "SELECT
			  p.post_id,
			  p.meta_value,
			  (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = p.post_id AND meta_key = '_wps3_image_meta') AS val
			FROM $wpdb->postmeta p
			WHERE p.post_id IN($in) AND p.meta_key = '_wps3_image_order'
			ORDER BY p.meta_value ASC";
	$meta = $wpdb->get_results($sql);
	foreach($meta as $meta_item) {
		
		$data = unserialize($meta_item->val);
		$images[] = array(
			'post_id'=>$meta_item->post_id,
			'image_link'=>$data['image_link'],
			'image_text'=>$data['image_text'],
			'span_location'=>$data['span_location'],
			'span_opacity'=>$data['span_opacity'],
			'span_bg_colour'=>$data['span_bg_colour'],
			'span_text_colour'=>$data['span_text_colour'],
			'order'=>$data['order'],
			'file'=>$data['file']
		);
	}
}

?>
<p>&nbsp;</p>
<div id="new-gallery-item-form">
<h3>Add a new image to the gallery</h3>
<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&amp;gid=<?php echo $gallery->id; ?>&amp;insert-item=">
<?php wp_nonce_field('item-add'); ?>
<table class="form-table">
  <tr valign="top" id="image-preview">
    <th scope="row" class="item-preview"></th>
    <td class="item-details"></td>
  </tr>
  <tr valign="top" id="image-select">
    <th scope="row">&nbsp;</th>
    <td><a href="<?php echo WP_PLUGIN_URL; ?>/fotoslide/fs_medialibrary.php?&amp;gid=<?php echo $gallery->id; ?>&amp;TB_iframe=true&amp;height=620&amp;width=700&amp;modal=true" class="button-secondary thickbox" id="select-media"><?php _e('Select Image From Media Library'); ?></a></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="image_link"><?php _e('Image Link'); ?></label></th>
    <td><input type="text" name="image_link" id="image_link" class="regular-text" value="" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="caption_text"><?php _e('Caption Message'); ?></label></th>
    <td><input type="text" name="caption_text" id="caption_text" class="regular-text" value="" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="caption_location"><?php _e('Caption Location'); ?></label></th>
    <td><select name="caption_location" id="caption_location">
    	<option value="top"><?php _e('Top'); ?></option>
        <option value="right"><?php _e('Right'); ?></option>
        <option value="bottom"><?php _e('Bottom'); ?></option>
        <option value="left"><?php _e('Left'); ?></option>
    </select></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="caption_opacity"><?php _e('Caption Opacity'); ?></label></th>
    <td><select name="caption_opacity" id="caption_opacity">
    	<?php for($i=5; $i<=100; $i = ($i+5)) : ?>
        <option value="<?php echo $i; ?>"<?php if($i==70) : ?> selected="selected"<?php endif; ?>><?php _e($i . '%'); ?></option>
        <?php endfor; ?>
    </select></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="caption_bg_colour"><?php _e('Caption Background Colour'); ?></label></th>
    <td><input type="text" name="caption_bg_colour" id="caption_bg_colour" class="regular-text" value="" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="caption_text_colour"><?php _e('Text Colour'); ?></label></th>
    <td><input type="text" name="caption_text_colour" id="caption_text_colour" class="regular-text" value="" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="image_order"><?php _e('Order'); ?></label></th>
    <td><select name="image_order">
    	<?php for($i=1;$i<=count($items)+1; $i++) : ?>
    	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    	<?php endfor; ?>
    </select></td>
  </tr>
  <tr valign="top">
    <th scope="row">&nbsp;</th>
    <td><input type="submit" class="button-primary" value="<?php _e('Insert'); ?>" /></td>
  </tr>
</table>
<input type="hidden" name="image_post_id" id="image_post_id" value="" />
</form>
</div><!-- new-gallery-image-form -->
<div class="clear"></div><br class="clear" /><p>&nbsp;</p>
<hr />
<div class="tablenav">
	<div class="alignleft">
    	<h3><?php _e('Gallery Images'); ?></h3>
    </div>
	<div class="alignright">
      <a href="#" class="button-secondary add-image-to-gallery"><?php _e('Add an image to this gallery'); ?></a>
    </div>
</div>
<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&amp;gid=<?php echo $gallery->id; ?>&amp;update=">
<?php wp_nonce_field('update-gallery-items'); ?>
<table class="widefat fixed" cellspacing="0">
  <thead>
    <tr>
      <th scope="col"><?php _e('Thumbnail'); ?></th>
      <th scope="col"><?php _e('Image Link'); ?></th>
      <th scope="col"><?php _e('Caption'); ?></th>
      <th scope="col"><?php _e('Location'); ?></th>
      <th scope="col"><?php _e('Opacity'); ?></th>
      <th scope="col"><?php _e('Caption Colour'); ?></th>
      <th scope="col"><?php _e('Text Colour'); ?></th>
      <th scope="col"><?php _e('Order'); ?></th>
      <th scope="col"><?php _e(' '); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th scope="col"><?php _e('Thumbnail'); ?></th>
      <th scope="col"><?php _e('Image Link'); ?></th>
      <th scope="col"><?php _e('Caption'); ?></th>
      <th scope="col"><?php _e('Location'); ?></th>
      <th scope="col"><?php _e('Opacity'); ?></th>
      <th scope="col"><?php _e('Caption Colour'); ?></th>
      <th scope="col"><?php _e('Text Colour'); ?></th>
      <th scope="col"><?php _e('Order'); ?></th>
      <th scope="col"><?php _e(' '); ?></th>
    </tr>
  </tfoot>
  <tbody>
  <?php if(isset($images)) : ?>
  	<!-- start items loop -->
    <?php $class = ''; $i = 0; foreach($images as $image) : $class = 'alternate' == $class ? '' : 'alternate'; ?>
    <tr class="<?php echo $class; ?>">
      <td><?php echo wp_get_attachment_image($image['post_id'],array(70,70));?></td>
      <td><input type="text" name="Images[<?php echo $i; ?>][image_link]" value="<?php echo stripslashes($image['image_link']); ?>" class="regular-text" />
      <input type="hidden" name="Images[<?php echo $i; ?>][post_id]" value="<?php echo $image['post_id']; ?>" /></td>
      <td><textarea name="Images[<?php echo $i; ?>][image_text]" cols="35" rows="4"><?php echo stripslashes($image['image_text']); ?></textarea></td>
      <td><select name="Images[<?php echo $i; ?>][span_location]">
      		<option value="top"<?php if($image['span_location']=='top'): ?> selected="selected"<?php endif; ?>><?php _e('Top'); ?></option>
            <option value="right"<?php if($image['span_location']=='right'): ?> selected="selected"<?php endif; ?>><?php _e('Right'); ?></option>
            <option value="bottom"<?php if($image['span_location']=='bottom'): ?> selected="selected"<?php endif; ?>><?php _e('Bottom'); ?></option>
            <option value="left"<?php if($image['span_location']=='left'): ?> selected="selected"<?php endif; ?>><?php _e('Left'); ?></option>
      </select></td>
	  <td><select name="Images[<?php echo $i; ?>][image_span_opacity]" id="image_span_opacity">
	    	<?php for($ii=5; $ii<=100; $ii = ($ii+5)) : ?>
	        <option value="<?php echo $ii; ?>"<?php if($ii==((float)$image['span_opacity']*100)) : ?> selected="selected"<?php endif; ?>><?php _e($ii . '%'); ?></option>
	        <?php endfor; ?>
	    </select></td>
	  <td><input type="text" name="Images[<?php echo $i; ?>][image_span_colour]" class="regular-text" value="<?php echo $image['span_bg_colour']; ?>" /></td>
	  <td><input type="text" name="Images[<?php echo $i; ?>][image_text_colour]" class="regular-text" value="<?php echo $image['span_text_colour']; ?>" /></td>
      <td><select name="Images[<?php echo $i; ?>][order]">
      		<?php for($ii=1; $ii<=count($images); $ii++) : ?>
            <option value="<?php echo $ii; ?>"<?php if((int)$image['order'] == $ii) : ?> selected="selected"<?php endif; ?>><?php echo $ii; ?></option>
            <?php endfor; ?>
      </select></td>
      <td><a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&gid=<?php echo $_GET['gid']; ?>&amp;remove=<?php echo $image['post_id']; ?>&amp;_wpnonce=<?php echo wp_create_nonce('remove-item'); ?>" class="button-secondary"><?php _e('Remove'); ?></a></td>
    </tr>
    <?php $i++; endforeach; ?>
    <!-- end items loop -->
    
  <?php else : ?>
    <tr>
      <td colspan="9" class="empty-table-data"><p><?php _e('There are currently no images in this gallery'); ?></p>
      <p><a href="#" class="button-secondary add-image-to-gallery"><?php _e('Add an image to this gallery'); ?></a></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>

<div class="tablenav">
	<div class="alignleft">
      <input type="submit" class="button-primary" value="Save changes to this gallery" />
    </div>
	<div class="alignright">
    	<a href="#" class="button-secondary add-image-to-gallery">Add a new image to this gallery</a>
    </div>
</div>
</form>
<div class="clear"></div><br class="clear" />
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($)
{
	$('#new-gallery-item-form').hide();
	$('tr#image-preview').hide();
	$('a.add-image-to-gallery').click(function() {
		$('#new-gallery-item-form').fadeIn(400, function() {
			$('a.add-image-to-gallery').fadeOut(200);
		});
		return false;
	});
});
//]]>
</script>

<?php endif; ?>