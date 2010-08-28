<?php

/**
 * This file renders the galleries tab contents
 */

global $wpdb;

// include the form processor
require_once(dirname(__FILE__).'/fs_processform.php');

// gallery pagination and query
$query = $wpdb->get_row("SELECT COUNT(*) as tot FROM ".FS_GALTBL);
$galleryCount = $query->tot;
if($galleryCount > 0) {
	require_once(dirname(__FILE__).'/pagination.class.php');
	$p = new pagination;
	$currentPage = isset($_GET['paging']) ? $_GET['paging'] : 1;
	$p->items($galleryCount);
	$p->limit(5);
	$p->target('upload.php?page=fotoslide');
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
      <th scope="col" width="25px" class="manage-column"><?php _e('#'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Gallery Name'); ?></th>
      <th scope="col" class="manag-column"><?php _e('Effect'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Size (w x h)'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Pause'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Speed'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Actions'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th scope="col" width="25px" class="manage-column"><?php _e('#'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Gallery Name'); ?></th>
      <th scope="col" class="manag-column"><?php _e('Effect'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Size (w x h)'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Pause'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Speed'); ?></th>
      <th scope="col" class="manage-column"><?php _e('Actions'); ?></th>
    </tr>
  </tfoot>
  <tbody class="list:post">
  <?php if($galleryCount > 0) : ?>
  
  	<!-- start gallery loop -->
    <?php $alt = false; foreach($wpdb->get_results("SELECT * FROM " .FS_GALTBL.' '.$limit) as $gallery) : ?>
    <tr id="fs-gallery-<?php echo $gallery->id; ?>" valign="top"<?php if($alt) : ?> class="alternate"<?php endif; $alt = $alt ? false : true; ?>>
      <td><?php echo $gallery->id; ?></td>
      <td><?php echo stripslashes($gallery->gallery_name); ?></td>
      <td><?php echo $gallery->effect; ?></td>
      <td><?php echo $gallery->width . ' x ' . $gallery->height; ?></td>
      <td><?php echo $gallery->pauseTime; ?></td>
      <td><?php echo $gallery->animSpeed; ?></td>
      <td>
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=edit-gallery&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/page_white_edit.png" alt="Edit" /></a> &nbsp; 
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=delete-gallery&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/page_white_delete.png" alt="Delete" /></a> &nbsp;
      <a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&amp;gid=<?php echo $gallery->id; ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/photos.png" alt="Items" /></a>
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
    <th scope="row"><label for="gallery_pause_time"><?php _e('Pause'); ?></label></th>
    <td><input type="text" name="gallery_pause_time" id="gallery_pause_time" class="regular-text" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_speed"><?php _e('Transition Speed'); ?></label></th>
    <td><input type="text" name="gallery_transition_speed" id="gallery_transition_speed" class="regular-text" /></td>
  </tr>
  
  <!-- since 2.0 -->
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_effect"><?php _e('Effect'); ?></label></th>
    <td><select name="gallery_transition_effect" id="gallery_transition_effect">
    		<option value="random" selected="selected"><?php _e('Random'); ?></option>
    		<option value="sliceDown"><?php _e('Slice Down'); ?></option>
    		<option value="sliceDownLeft"><?php _e('Slice Down Left'); ?></option>
    		<option value="sliceUp"><?php _e('Slice Up'); ?></option>
    		<option value="sliceUpLeft"><?php _e('Slice Up Left'); ?></option>
    		<option value="sliceUpDown"><?php _e('Slice Up Down'); ?></option>
    		<option value="sliceUpDownLeft"><?php _e('Slice Up Down Left'); ?></option>
    		<option value="fold"><?php _e('Fold'); ?></option>
    		<option value="fade"><?php _e('Fade'); ?></option>
    	</select></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_slices"><?php _e('Slices'); ?></label></th>
    <td><input type="text" name="gallery_slices" id="gallery_slices" class="regular-text" value="15" /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_direction_nav"><?php _e('Add Direction Nav '); ?></label></th>
    <td><input type="checkbox" name="gallery_direction_nav" id="gallery_direction_nav"  /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_direction_nav_on_hover"><?php _e('Show direction on hover'); ?></label></th>
    <td><input type="checkbox" name="gallery_direction_nav_on_hover" id="gallery_direction_nav_on_hover" /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_randomize_first"><?php _e('Randomize first slide'); ?></label></th>
    <td><input type="checkbox" name="gallery_randomize_first" id="gallery_randomize_first" /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_control_nav"><?php _e('Control nav (1,2,3 etc.)'); ?></label></th>
    <td><input type="checkbox" name="gallery_control_nav" id="gallery_control_nav" /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_caption_opacity"><?php _e('Caption Opacity'); ?></label></th>
    <td><select name="gallery_caption_opacity" id="gallery_caption_opacity">
    	<?php for($i=10; $i<=100; $i = ($i+10)) : ?>
        <option value="<?php echo $i; ?>"<?php if($i==70) : ?> selected="selected"<?php endif; ?>><?php _e($i . '%'); ?></option>
        <?php endfor; ?>
    </select></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_class_attribute"><?php _e('Caption Class Attribute'); ?></label></th>
    <td><input type="text" name="gallery_class_attribute" id="gallery_class_attribute" class="regular-text" value="fotoslide" /></td>
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
$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_GALTBL." WHERE id = %d", array($_GET['gid'])));
?>
<p>&nbsp;</p>
<hr />
<h3><?php _e('Edit Gallery'); ?></h3>
<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=edit-gallery&amp;gid=<?php echo $gallery->id; ?>&amp;update=">
<?php wp_nonce_field('edit-gallery'); ?>
<table class="form-table" id="gallery-details">

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
    <th scope="row"><label for="gallery_pause_time"><?php _e('Pause'); ?></label></th>
    <td><input type="text" name="gallery_pause_time" id="gallery_pause_time" class="regular-text" value="<?php echo $gallery->pauseTime; ?>" /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_speed"><?php _e('Transition Speed'); ?></label></th>
    <td><input type="text" name="gallery_transition_speed" id="gallery_transition_speed" class="regular-text" value="<?php echo $gallery->animSpeed; ?>" /></td>
  </tr>
  
  <!-- since 2.0 -->
  <tr valign="top">
    <th scope="row"><label for="gallery_transition_effect"><?php _e('Effect'); ?></label></th>
    <td><select name="gallery_transition_effect" id="gallery_transition_effect">
    		<option value="random"<?php if($gallery->effect == 'random') : ?> selected="selected"<?php endif; ?>><?php _e('Random'); ?></option>
    		<option value="sliceDown"<?php if($gallery->effect == 'sliceDown') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Down'); ?></option>
    		<option value="sliceDownLeft"<?php if($gallery->effect == 'sliceDownLeft') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Down Left'); ?></option>
    		<option value="sliceUp"<?php if($gallery->effect == 'sliceUp') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Up'); ?></option>
    		<option value="sliceUpLeft"<?php if($gallery->effect == 'sliceUpLeft') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Up Left'); ?></option>
    		<option value="sliceUpDown"<?php if($gallery->effect == 'sliceUpDown') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Up Down'); ?></option>
    		<option value="sliceUpDownLeft"<?php if($gallery->effect == 'sliceUpDownLeft') : ?> selected="selected"<?php endif; ?>><?php _e('Slice Up Down Left'); ?></option>
    		<option value="fold"<?php if($gallery->effect == 'fold') : ?> selected="selected"<?php endif; ?>><?php _e('Fold'); ?></option>
    		<option value="fade"<?php if($gallery->effect == 'fade') : ?> selected="selected"<?php endif; ?>><?php _e('Fade'); ?></option>
    	</select></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_slices"><?php _e('Slices'); ?></label></th>
    <td><input type="text" name="gallery_slices" id="gallery_slices" class="regular-text" value="<?php echo $gallery->slices; ?>" /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_direction_nav"><?php _e('Add Direction Nav '); ?></label></th>
    <td><input type="checkbox" name="gallery_direction_nav" id="gallery_direction_nav"<?php if($gallery->directionNav==1) : ?> checked="checked"<?php endif; ?>  /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_direction_nav_on_hover"><?php _e('Show direction on hover'); ?></label></th>
    <td><input type="checkbox" name="gallery_direction_nav_on_hover" id="gallery_direction_nav_on_hover"<?php if($gallery->directionNavHide==1) : ?> checked="checked"<?php endif; ?>  /></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_control_nav"><?php _e('Control nav (1,2,3 etc.)'); ?></label></th>
    <td><input type="checkbox" name="gallery_control_nav" id="gallery_control_nav"<?php if($gallery->controlNav==1) : ?> checked="checked"<?php endif; ?>  /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_randomize_first"><?php _e('Randomize first slide'); ?></label></th>
    <td><input type="checkbox" name="gallery_randomize_first" id="gallery_randomize_first"<?php if($gallery->randomize_first==1) : ?> checked="checked"<?php endif; ?> /></td>
  </tr>
  
  <tr valign="top">
    <th scope="row"><label for="gallery_caption_opacity"><?php _e('Caption Opacity'); ?></label></th>
    <td><select name="gallery_caption_opacity" id="gallery_caption_opacity">
    	<?php for($i=10; $i<=100; $i = ($i+10)) : ?>
        <option value="<?php echo $i; ?>"<?php if($i==($gallery->captionOpacity)*100) : ?> selected="selected"<?php endif; ?>><?php _e($i . '%'); ?></option>
        <?php endfor; ?>
    </select></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="gallery_class_attribute"><?php _e('Caption Class Attribute'); ?></label></th>
    <td><input type="text" name="gallery_class_attribute" id="gallery_class_attribute" class="regular-text" value="<?php echo $gallery->class_attribute; ?>" /></td>
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
$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_GALTBL." WHERE id = %d", array($_GET['gid'])));
$items = $gallery ? $wpdb->get_results($wpdb->prepare('SELECT * FROM '.FS_ITEMTBL.' WHERE gallery_id = %d ORDER BY order_num ASC',array($_GET['gid']))) : false;

?>
<p>&nbsp;</p>
<div id="new-gallery-item-form">
<h3><?php _e('Add a new image to the gallery'); ?></h3>
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
    <td><textarea cols="50" rows="10" name="caption_text" id="caption_text" class="large-text code"></textarea></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="image_order"><?php _e('Order'); ?></label></th>
    <td><select name="image_order">
    	<?php for($i=1;$i<=count($items)+1; $i++) : ?>
    	<option value="<?php echo $i; ?>"<?php if($i==(count($items)+1)) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
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

<?php if($items) :?>
<div class="tablenav">
	<div class="alignleft">
    	<h3><?php _e('Gallery Images for &#8220;' . $gallery->gallery_name . '&#8221; gallery'); ?></h3>
    </div>
	<div class="alignright">
      <a href="#" class="button-secondary add-image-to-gallery"><?php _e('Add an image to this gallery'); ?></a>
    </div>
</div>
<?php else : ?>
<div class="tablenav">
  <div class="alignleft">
    <h3><?php _e('This gallery is currently has no images'); ?></h3>
  </div>
  <div class="alignright">
      <a href="#" class="button-secondary add-image-to-gallery"><?php _e('Add an image to this gallery'); ?></a>
    </div>
</div>
<?php endif; ?>
<br />


<form method="post" action="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&amp;gid=<?php echo $gallery->id; ?>&amp;update=">
<?php wp_nonce_field('update-gallery-items'); ?>
<?php if(isset($images)) : ?>
<div class="tablenav">
	<div class="alignleft">
      <input type="submit" class="button-primary" value="Save changes to this gallery" />
    </div>
</div>
<?php endif; ?>
<?php if($items) : $i=0; $class = 'alt'; foreach($items as $image) : ?>
<div class="fs-gal-item <?php echo $class; ?>">

  <input type="hidden" name="Images[<?php echo $i; ?>][post_id]" value="<?php echo $image->post_id; ?>" />
  
  <!-- gallery thumbnail -->
  <div class="fs-gal-item-thumbnail">
  <?php echo wp_get_attachment_image($image->post_id,array(100,100));?>
  </div>
  
  <!-- gallery input elements -->
  <div class="fs-gal-item-elements">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for=""><?php _e('Image Link')?></label></th>
          <td><input type="text" name="Images[<?php echo $i; ?>][image_link]" value="<?php echo stripslashes($image->href); ?>" class="regular-text" /></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for=""><?php _e('Caption')?></label></th>
          <td><textarea class="large-text" name="Images[<?php echo $i; ?>][caption_text]" cols="35" rows="10"><?php echo stripslashes($image->caption_text); ?></textarea></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for=""><?php _e('Order')?></label></th>
          <td><select name="Images[<?php echo $i; ?>][order]">
		      		<?php for($ii=1; $ii<=count($items); $ii++) : ?>
		            <option value="<?php echo $ii; ?>"<?php if((int)$image->order_num == $ii) : ?> selected="selected"<?php endif; ?>><?php echo $ii; ?></option>
		            <?php endfor; ?>
		      </select>
		      <input type="hidden" name="Images[<?php echo $i; ?>][id]" value="<?php echo $image->id; ?>" />
      	  </td>
        </tr>
        <tr valign="top">
          <th scope="row">&nbsp;</th>
          <td><a href="<?php echo WP_PLUGIN_BASE_URL; ?>&amp;action=gallery-items&gid=<?php echo $_GET['gid']; ?>&amp;remove=<?php echo $image->post_id; ?>&amp;_wpnonce=<?php echo wp_create_nonce('remove-item'); ?>" class="button-secondary"><?php _e('Remove'); ?></a></td>
        </tr>
      </tbody>
    </table>
  </div>
  
</div>
<?php $class = $class == 'alt' ? '' : 'alt'; $i++; endforeach; endif; ?>

<?php if($items) : ?>
<div class="tablenav">
	<div class="alignleft">
      <input type="submit" class="button-primary" value="Save changes to this gallery" />
    </div>
	<div class="alignright">
    	<a href="#" class="button-secondary add-image-to-gallery"><?php _e('Add a new image to this gallery'); ?></a>
    </div>
</div>
<?php endif; ?>
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