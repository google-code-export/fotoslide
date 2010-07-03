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
    <?php $alt = false; foreach($wpdb->get_results("SELECT * FROM " .FS_TABLENAME. $limit) as $gallery) : ?>
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