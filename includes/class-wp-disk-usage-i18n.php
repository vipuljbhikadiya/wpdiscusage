<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/includes
 * @author     Vipul Bhikadiya <vipulbhikadiya1991@gmail.com>
 */
class Wp_Disk_Usage_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-disk-usage',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
