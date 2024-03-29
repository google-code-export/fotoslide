<?php

/**
 * This file processes all the admin forms
 */


// variable for form outcome
$message = array(
	'output'=>false,
	'type'=>'success',
	'message'=>'',
	'action'=>isset($_GET['action']) ? $_GET['action'] : '',
	'showform'=>true
);

// form action variable
$action = isset($_GET['action']) ? $_GET['action'] : '';


// Delete a gallery
if($action == 'delete-gallery' && isset($_GET['confirm']) && isset($_GET['gid'])) {
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'delete-gallery')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'delete-gallery',
			'showform'=>true
		);
	} else {
		$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($_GET['gid'])));
		if( isset( $gallery ) ) {
			$items = unserialize($gallery->items);

			// delete unused postmeta
			if( !empty( $items ) ) {
				$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_fs_%' AND post_id IN(".implode(',', $items).")";
				$wpdb->query($sql);
			}
			
			$sql = "DELETE FROM ".FS_TABLENAME." WHERE id=".$gallery->id;
			$wpdb->query($sql);
			$message = array(
				'output'=>true,
				'type'=>'success',
				'message'=>'Gallery deleted succesfully',
				'action'=>'delete-gallery',
				'showform'=>false
			);
		}else {
			
			$message = array(
				'output'=>true,
				'type'=>'err',
				'message'=>'Invalid gallery selected',
				'action'=>'delete-gallery',
				'showform'=>true
			);
		}		
	}		
}


// process new gallery request
elseif($action == 'new-gallery' && isset($_REQUEST['_wpnonce']) && isset($_GET['insert'])) {
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'new-gallery')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'new-gallery',
			'showform'=>true
		);
	} else {
		
		// check required values
		if(empty($_POST['gallery_name'])) {
			$message = array(
				'output'=>true,
				'type'=>'err',
				'message'=>'Please supply a gallery name',
				'action'=>'new-gallery',
				'showform'=>true
			);
		} else {
			$query = $wpdb->insert(FS_TABLENAME,
							array(
								'gallery_name'=>$_POST['gallery_name'],
								'dstamp'=>date('Y-m-d 00:00:00'),
								'width'=>empty($_POST['gallery_width']) ? 400 : (int)$_POST['gallery_width'],
								'height'=>empty($_POST['gallery_height']) ? 200 : (int)$_POST['gallery_height'],
								'timeout'=>empty($_POST['gallery_timeout']) ? 4000 : (int)$_POST['gallery_timeout'],
								'items'=>serialize(array()),
								'transition_speed'=>empty($_POST['gallery_transition_speed']) ? 1000 : (int)$_POST['gallery_transition_speed']
							),
							array('%s','%s','%d','%d','%d','%s','%d')
						);
			
			if(!$query) {
				$message = array(
					'output'=>true,
					'type'=>'err',
					'message'=>'there was an error inserting data to the database',
					'action'=>'new-gallery',
					'showform'=>true
				);
			} else {
				$message = array(
					'output'=>true,
					'type'=>'success',
					'message'=>'Gallery created succesfully',
					'action'=>'new-gallery',
					'showform'=>true
				);
			}
		}
	}
}



// process gallery update
elseif($action=='edit-gallery' && isset($_GET['update']) && isset($_GET['gid']) && isset($_REQUEST['_wpnonce'])) {
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'edit-gallery')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'edit-gallery',
			'showform'=>true
		);
	} else {
		
		// check required values
		if(empty($_POST['gallery_name'])) {
			$message = array(
				'output'=>true,
				'type'=>'err',
				'message'=>'Please supply a gallery name',
				'action'=>'edit-gallery',
				'showform'=>true
			);
		} else {
			
			$wpdb->update(FS_TABLENAME,
					array(
						'gallery_name'=>$_POST['gallery_name'],
						'width'=>empty($_POST['gallery_width']) ? 400 : (int)$_POST['gallery_width'],
						'height'=>empty($_POST['gallery_height']) ? 200 : (int)$_POST['gallery_height'],
						'timeout'=>empty($_POST['gallery_timeout']) ? 4000 : (int)$_POST['gallery_timeout'],
						'transition_speed'=>empty($_POST['gallery_transition_speed']) ? 1000 : (int)$_POST['gallery_transition_speed']
					),
					array('id'=>$_GET['gid']),
					array('%s','%d','%d','%d','%d'),
					array('%d')
				);
			
			$message = array(
				'output'=>true,
				'type'=>'success',
				'message'=>'Gallery updated succesfully',
				'action'=>'edit-gallery',
				'showform'=>true
			);
		}
	}
}


// add item
elseif($action == 'gallery-items' && isset($_GET['gid']) && isset($_GET['insert-item']) && isset($_REQUEST['_wpnonce'])) {
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'item-add')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'gallery-items',
			'showform'=>true
		);
	} else {
		if(!isset($_POST['image_post_id']) || (isset($_POST['image_post_id']) && empty($_POST['image_post_id']))) {
			$message = array(
				'output'=>true,
				'type'=>'err',
				'message'=>'No image selected',
				'action'=>'gallery-items',
				'showform'=>true
			);
		} else {
			$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($_GET['gid'])));
			if(!$gallery) {
				$message = array(
					'output'=>true,
					'type'=>'err',
					'message'=>'Invalid gallery selected - Does it really exist?',
					'action'=>'gallery-items',
					'showform'=>true
				);
			} elseif(in_array((int)$_POST['image_post_id'], unserialize($gallery->items))) {
				$message = array(
					'output'=>true,
					'type'=>'err',
					'message'=>'Image already exists in your gallery',
					'action'=>'gallery-items',
					'showform'=>true
				);
			} else {
				// add image to gallery
				$items = unserialize($gallery->items);
				array_push($items, (int)$_POST['image_post_id']);
				$wpdb->update(FS_TABLENAME,
							  array('items'=>serialize($items)),
							  array('id'=>$_GET['gid']),
							  array('%s'),
							  array('%d'));
				
				// add post meta with specifics
				$imageMeta = array(
					'image_link' => addslashes((string)$_POST['image_link']),
					'caption_text' => addslashes((string)$_POST['caption_text']),
					'caption_location' => (string)$_POST['caption_location'],
					'caption_opacity'=>round(((int)$_POST['caption_opacity']/100),2),
					'caption_bg_colour'=>addslashes((string)$_POST['caption_bg_colour']),
					'caption_text_colour'=>addslashes((string)$_POST['caption_text_colour']),
					'order'=> (int)$_POST['image_order'],
					'file'=>getUploadPath().get_post_meta((int)$_POST['image_post_id'], '_wp_attached_file', true)
				);
				add_post_meta((int)$_POST['image_post_id'], '_fs_image_meta', $imageMeta, true);
				add_post_meta((int)$_POST['image_post_id'], '_fs_image_order', (int)$_POST['image_order'], true);
				
				$message = array(
					'output'=>true,
					'type'=>'success',
					'message'=>'Image added to &#8220;' . $gallery->gallery_name . '&#8221; gallery',
					'action'=>'gallery-items',
					'showform'=>true
				);
			}
		}
	}
}


// update gallery items
elseif($action == 'gallery-items' && isset($_GET['gid']) && isset($_GET['update']) && isset($_REQUEST['_wpnonce']) && isset($_POST['Images'])) {
	if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'update-gallery-items')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'gallery-items',
			'showform'=>true
		);
	} else {
		foreach($_POST['Images'] as $image) {
			$imageMeta = array(
					'image_link' => addslashes((string)$image['image_link']),
					'caption_text' => addslashes((string)$image['caption_text']),
					'caption_location' => (string)$image['caption_location'],
					'caption_opacity'=>round(((int)$image['caption_opacity']/100),2),
					'caption_bg_colour'=>addslashes((string)$image['caption_bg_colour']),
					'caption_text_colour'=>addslashes((string)$image['caption_text_colour']),
					'order'=> (int)$image['order'],
					'file'=>getUploadPath().get_post_meta((int)$image['post_id'], '_wp_attached_file', true)
				);
			update_post_meta((int)$image['post_id'],'_fs_image_meta',$imageMeta);
			update_post_meta((int)$image['post_id'],'_fs_image_order',(int)$image['order']);
		}
		
		$message = array(
			'output'=>true,
			'type'=>'success',
			'message'=>'Gallery items updated succesfully',
			'action'=>'gallery-items',
			'showform'=>true
		);
	}
}


// remove gallery item
elseif($action == 'gallery-items' && isset($_GET['gid']) && isset($_GET['remove']) && isset($_GET['_wpnonce'])) {
	if(!wp_verify_nonce($_GET['_wpnonce'], 'remove-item')) {
		$message = array(
			'output'=>true,
			'type'=>'err',
			'message'=>'Security check failed',
			'action'=>'gallery-items',
			'showform'=>true
		);
	} else {
		$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_TABLENAME." WHERE id = %d",array($_GET['gid'])));
		if(!in_array((int)$_GET['remove'], unserialize($gallery->items))) {
			$message = array(
				'output'=>true,
				'type'=>'err',
				'message'=>'Image not in gallery',
				'action'=>'gallery-items',
				'showform'=>true
			);
		} else {
			$new = array_diff(unserialize($gallery->items),array((int)$_GET['remove']));
			$wpdb->update(FS_TABLENAME,
						  array('items'=>serialize($new)),
						  array('id'=>$gallery->id),
						  array('%s'),
						  array('%d'));
			
			delete_post_meta((int)$_GET['remove'], '_fs_image_meta');
			delete_post_meta((int)$_GET['remove'], '_fs_image_order');
			
			$message = array(
				'output'=>true,
				'type'=>'success',
				'message'=>'Image removed from gallery',
				'action'=>'gallery-items',
				'showform'=>true
			);
		}
	}
}