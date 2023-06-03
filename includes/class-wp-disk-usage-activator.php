<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/includes
 * @author     Vipul Bhikadiya <vipulbhikadiya1991@gmail.com>
 */
class Wp_Disk_Usage_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpdu_log';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name ( 
			`wpdu_log_id` BIGINT NOT NULL AUTO_INCREMENT, 
			`disc_statistics` LONGTEXT NOT NULL, 
			`created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			PRIMARY KEY (`wpdu_log_id`)
			) $charset_collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
