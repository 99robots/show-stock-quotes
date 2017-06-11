<?php
	
	if(!defined('WP_UNINSTALL_PLUGIN') ) {
    	die();
    }
    
    /* Delete all existence of this plugin */

	global $wpdb;
	
	$version_name = 'kjb_show_stock_quotes_version';
	
	if ( !is_multisite() ) {
	
		// Delete blog option
		
		delete_option($version_name);
		
	} else {
		// Delete site option
	
		delete_site_option($version_name);
	}
?>