<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org
 * @since             1.0.0
 * @package           Wp_Disk_Usage
 *
 * @wordpress-plugin
 * Plugin Name:       WP Disk Usage
 * Plugin URI:        https://wordpress.org
 * Description:       WordPress plugin that displays the disk usage of a website.
 * Version:           1.0.0
 * Author:            Vipul Bhikadiya
 * Author URI:        https://wordpress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-disk-usage
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_DISK_USAGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-disk-usage-activator.php
 */
function activate_wp_disk_usage() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-disk-usage-activator.php';
	Wp_Disk_Usage_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-disk-usage-deactivator.php
 */
function deactivate_wp_disk_usage() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-disk-usage-deactivator.php';
	Wp_Disk_Usage_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_disk_usage' );
register_deactivation_hook( __FILE__, 'deactivate_wp_disk_usage' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-disk-usage.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_disk_usage() {

	$plugin = new Wp_Disk_Usage();
	$plugin->run();

}
run_wp_disk_usage();
