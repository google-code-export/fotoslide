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
		$wpdb->query($wpdb->prepare('DELETE FROM '.FS_GALTBL.' WHERE id = %d',array($_GET['gid'])));
		$wpdb->query($wpdb->prepare('DELETE FROM '.FS_ITEMTBL.' WHERE gallery_id = %d',array($_GET['gid'])));
		$message = array(
			'output'=>true,
			'type'=>'success',
			'message'=>'Gallery deleted succesfully',
			'action'=>'delete-gallery',
			'showform'=>false
		);
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
			$query = $wpdb->insert(FS_GALTBL,
							array(
								'gallery_name'=>$_POST['gallery_name'],
								'dstamp'=>date('Y-m-d H:i:s'),
								'width'=>empty($_POST['gallery_width']) ? 400 : (int)$_POST['gallery_width'],
								'height'=>empty($_POST['gallery_height']) ? 200 : (int)$_POST['gallery_height'],
								'pauseTime'=>empty($_POST['gallery_pause_time']) ? 4000 : (int)$_POST['gallery_pause_time'],
								'animSpeed'=>empty($_POST['gallery_transition_speed']) ? 1000 : (int)$_POST['gallery_transition_speed'],
								'effect'=>$_POST['gallery_transition_effect'],
								'captionOpacity'=>(int)$_POST['gallery_caption_opacity'] / 100,
								'class_attribute'=>$_POST['gallery_class_attribute'],
								'slices'=>$_POST['gallery_slices'],
								'directionNav'=>isset($_POST['gallery_direction_nav']) ? 1 : 0,
								'directionNavHide'=>isset($_POST['gallery_direction_nav_on_hover']) ? 1 : 0,
								'controlNav'=>isset($_POST['gallery_control_nav']) ? 1 : 0,
								'randomize_first'=>isset($_POST['gallery_randomize_first']) ? 1 : 0
								
							),
							array('%s','%s','%d','%d','%d','%d','%s','%s','%s','%d','%d','%d','%d','%d')
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
			
			$wpdb->update(FS_GALTBL,
					array(
						'gallery_name'=>$_POST['gallery_name'],
						'width'=>empty($_POST['gallery_width']) ? 400 : (int)$_POST['gallery_width'],
						'height'=>empty($_POST['gallery_height']) ? 200 : (int)$_POST['gallery_height'],
						'pauseTime'=>empty($_POST['gallery_pause_time']) ? 4000 : (int)$_POST['gallery_pause_time'],
						'animSpeed'=>empty($_POST['gallery_transition_speed']) ? 1000 : (int)$_POST['gallery_transition_speed'],
						'effect'=>$_POST['gallery_transition_effect'],
						'captionOpacity'=>(int)$_POST['gallery_caption_opacity'] / 100,
						'class_attribute'=>$_POST['gallery_class_attribute'],
						'slices'=>$_POST['gallery_slices'],
						'directionNav'=>isset($_POST['gallery_direction_nav']) ? 1 : 0,
						'directionNavHide'=>isset($_POST['gallery_direction_nav_on_hover']) ? 1 : 0,
						'controlNav'=>isset($_POST['gallery_control_nav']) ? 1 : 0,
						'randomize_first'=>isset($_POST['gallery_randomize_first']) ? 1 : 0
					),
					array('id'=>$_GET['gid']),
					array('%s','%d','%d','%d','%d','%s','%s','%s','%d','%d','%d','%d','%d'),
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
			$gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".FS_GALTBL." WHERE id = %d",array($_GET['gid'])));
			if(!$gallery) {
				$message = array(
					'output'=>true,
					'type'=>'err',
					'message'=>'Invalid gallery selected - Does it really exist?',
					'action'=>'gallery-items',
					'showform'=>true
				);
			} else {
				// add image to gallery
				$wpdb->insert(FS_ITEMTBL,array(
					'post_id'=>$_POST['image_post_id'],
					'caption_text'=>$_POST['caption_text'],
					'href'=>$_POST['image_link'],
					'gallery_id'=>$_GET['gid'],
					'order_num'=>$_POST['image_order']
				),array('%d','%s','%s','%d','%d'));
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
			$wpdb->update(FS_ITEMTBL,array(
				'caption_text'=>$image['caption_text'],
				'href'=>$image['image_link'],
				'order_num'=>$image['order']
			),
			array('id'=>$image['id']),
			array('%s','%s','%d'),
			array('%d')
			);
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
		$wpdb->query($wpdb->prepare('DELETE FROM '.FS_ITEMTBL.' WHERE gallery_id = %d AND post_id = %d',
			array($_GET['gid'],$_GET['remove'])));
			
		$message = array(
			'output'=>true,
			'type'=>'success',
			'message'=>'Image removed from gallery',
			'action'=>'gallery-items',
			'showform'=>true
		);
	}
}