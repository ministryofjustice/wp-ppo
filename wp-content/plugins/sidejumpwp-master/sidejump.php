<?php
/**
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 * @link      http://sidejump.net
 * @copyright 2014 - Studio Hyperset, Inc.
 *
 * @wordpress-plugin
 * Plugin Name:       Sidejump
 * Plugin URI:        http://sidejump.net
 * Description:       Sidejump helps synchronize development, staging, and production instances of WordPress.
 * Version:           1.0
 * Author:            Studio Hyperset, Inc. <info@studiohyperset.com>
 * Author URI:        http://studiohyperset.com
 * Text Domain:       sidejump-local
 * License:           GPLv3 or later
 * Domain Path:       /languages
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check for required PHP version

if (version_compare(PHP_VERSION, '5.2.0', '<')) {

    wp_die(sprintf('Sidejump Plugin requires PHP 5.2 or higher. You’re still on %s.',PHP_VERSION));
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/


require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-sync.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'WP_Sync', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Sync', 'deactivate' ) );

//
add_action( 'plugins_loaded', array( 'WP_Sync', 'get_instance' ) );


/**
 * Add links to Plugins Page
 *
 * @since    1.0.0
 * @return   void
 */

add_filter( "plugin_row_meta", 'wps_add_menu_plugins_page', 10, 2 );

function wps_add_menu_plugins_page($args,$argsb) {

	$plugin_name = plugin_basename( __FILE__ );

	if ($argsb == $plugin_name) {

		// $args[] = '<a href="http://sidejump.net/" target="_blank">Visit plugin site</a>';
		$args[] = '<a href="admin.php?page=sidejump">Settings</a>';
		$args[] = '<a href="http://sidejump.net/documentation-and-resources/" target="_blank">Documentation & Resources</a>';
		$args[] = '<a href="http://sidejump.net/terms/" target="_blank">Terms</a>';

	}

	return $args;
    
}

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
 
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-sync-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Sync_Admin', 'get_instance' ) );

}
