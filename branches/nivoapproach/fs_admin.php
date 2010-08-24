<?php

/**
 * The WordPress menu builder
 *
 * @return	null
 */
function fs_menus()
{
	add_media_page('FotoSlide','FotoSlide','manage_options','fotoslide','fs_main_menu');
}


/**
 * The main menu
 *
 * @return null
 */
function fs_main_menu()
{
	?>
    <div class="wrap">
    	<div id="icon-themes" class="icon32"></div>
        <h2><?php _e('FotoSlide'); ?></h2>
        <!-- JUI Tabs -->
        <div id="fs-tabs">
        	
            <ul>
              <li><a href="#fs-tab-1"><?php _e('Galleries'); ?></a></li>
              <li><a href="#fs-tab-2"><?php _e('Documentation'); ?></a></li>
            </ul>
            
            <!-- FotoSlide Galleries -->
            <div id="fs-tab-1">
            <?php require_once(dirname(__FILE__).'/fs_galleries.php'); ?>
            </div>
            <!-- END GALLERIES -->
            
            <!-- FotoSlide Documentation -->
            <div id="fs-tab-2">
            <?php require_once(dirname(__FILE__).'/fs_doc.php'); ?>
            </div>
            <!-- END Documentation -->
            
        </div>
        <!-- end JUI Tabs -->
        
    </div>
    <?php
}
add_action('admin_menu','fs_menus');


/**
 * Admin head
 *
 * load the requried files for the admin screens
 * to work. 
 *
 */
function fs_admin_head()
{
	wp_register_style('jui-style', WP_PLUGIN_URL . '/fotoslide/assets/jquery-ui-1.7.3.custom.css');
	wp_register_style('fs-admin', WP_PLUGIN_URL . '/fotoslide/assets/admin.css');
	wp_print_styles('jui-style');
	wp_print_styles('fs-admin');
	wp_print_scripts('jquery');
	wp_print_scripts('thickbox');
	wp_print_scripts('jquery-ui-core');
	wp_print_scripts('jquery-ui-tabs');

}
add_action('admin_head','fs_admin_head');


/**
 * Admin footer
 */
function fs_admin_footer()
{
	?>
    <script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function($) {
		$('#fs-tabs').tabs({selected:0});
		<?php if(isset($_GET['gid'])) : ?>
		$('tr#fs-gallery-<?php echo (int)$_GET['gid']; ?>').addClass('selected');
		<?php endif; ?>
	});
	//]]>
	</script>
    <?php
}
add_action('admin_footer','fs_admin_footer');