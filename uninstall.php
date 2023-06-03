<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( isset( $_GET['action'] ) && 'deactivate' === $_GET['action'] ) {

	global $wpdb;

	$table_name = $wpdb->prefix . 'wpdu_log';

	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	delete_option( 'worker_time' );
}
