<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks like
 * enqueue the admin-specific stylesheet and JavaScript, registering menu and other callbacks.
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/admin
 * @author     Vipul Bhikadiya <vipulbhikadiya1991@gmail.com>
 */
class Wp_Disk_Usage_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-disk-usage-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$wpdu_params = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-disk-usage-admin.js', array(), $this->version, false );

		wp_localize_script( $this->plugin_name, 'wpdu_params', $wpdu_params );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function wpds_setting_menu_page() {

		if ( is_admin() ) {
			// Check currenr user is administrator.
			if ( current_user_can( 'manage_options' ) ) {
				add_menu_page(
					__( 'Disc Usage', 'wp-disk-usage' ),
					__( 'Disc Usage', 'wp-disk-usage' ),
					'manage_options',
					'wpdu-manage',
					array( $this, 'wpdu_main_page' ),
					'dashicons-welcome-view-site',
					3
				);
		
				add_submenu_page(
					'wpdu-manage',
					esc_html__( 'Settings', 'wp-disk-usage' ),
					esc_html__( 'Settings', 'wp-disk-usage' ),
					'manage_options',
					'wpdu-setting',
					array( $this, 'wpdu_setting_page' )
				);
			}
		}

	}

	/**
	 * Add page to admin menu callback
	 */
	public function wpdu_main_page() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'wpdu_log';

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$table_name` ORDER BY %s DESC LIMIT 1", 'created_date' ) );

		require_once plugin_dir_path( __FILE__ ) . 'partials/wpdu-main-template.php';

	}

	/**
	 * Add page to admin menu callback
	 */
	public function wpdu_setting_page() {

		require_once plugin_dir_path( __FILE__ ) . 'partials/wpdu-setting-template.php';

	}

	/**
	 * Register WPDU default settings
	 */
	public function wpdu_setting_options() {

		register_setting( 'wpdu_settings', 'worker_time', array( 'default' => 5 ) );

	}

	/**
	 * Statistics generation ajax callback function
	 */
	public function wpdu_generate_statistics() {

		$directory  = isset( $_POST['directory'] ) ? wp_unslash( $_POST['directory'] ) : rtrim( ABSPATH, '/' ) . '/';
		$offset     = isset( $_POST['offset'] ) ? wp_unslash( intval( $_POST['offset'] ) ) : 0;
		$chunk_size = isset( $_POST['chunk_size'] ) ? intval($_POST['chunk_size']) : 50; // Default chunk size

		$totalSpace = disk_total_space( $directory );
		$freeSpace  = disk_free_space( $directory );
		$usedSpace  = $totalSpace - $freeSpace;

		$items = [];
		$startTime   = microtime( true );
		$worker_time = get_option( 'worker_time', 5 ); // Default will be 5

		if ( $handle = opendir( $directory ) ) {
			$count = 0;
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( $entry != "." && $entry != ".." ) {
					if ( $count >= $offset ) {
						$item = [
							'name' => $entry,
							'path' => $directory . '/' . $entry
						];

						if ( is_dir( $item['path'] ) ) {
							$directory_size = $this->getDirectorySize( $item['path'] );

							$item['type']        = 'directory';
							$item['size']        = $directory_size;
							$item['format_size'] = size_format( $directory_size, 2 );
							$item['percentate']  = round( ( $directory_size / $totalSpace ) * 100, 2 );
						} else {
							$directory_size = $this->getDirectorySize( dirname( $item['path'] ) );

							$fize_size = wp_filesize( $item['path'] );

							$item['type']        = 'file';
							$item['size']        = $fize_size;
							$item['format_size'] = size_format( $fize_size );
							$item['percentate']  = round( ( $fize_size / $directory_size ) * 100, 2 );
						}

						array_push( $items, $item );

						// Check if reached chunk size or worker time limit
						if ( count( $items ) >= $chunk_size || microtime( true ) - $startTime >=  $worker_time ) {
							break;
						}
					}

					$count++;
				}
			}

			closedir( $handle );
		}

		$result = [
			'directory'  => $directory,
			'offset'     => ( $offset + $chunk_size ),
			'chunk_size' => $chunk_size,
			'items'      => $items
		];

		wp_send_json_success( $result );
		exit;
	}

	/**
	 * Get directory size
	 *
	 * @param  string $directory The directory path.
	 * @return int    $totalSize Size of directory
	 */
	protected function getDirectorySize( $directory = '' ) {

		$totalSize = 0;
	
		if ( ! empty( $directory) ) {
			if ( $handle = opendir( $directory ) ) {
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( $entry != "." && $entry != ".." ) {
						$path = $directory . '/' . $entry;

						if ( is_dir( $path ) ) {
							$totalSize += $this->getDirectorySize( $path );
						} else {
							$totalSize += filesize( $path );
						}
					}
				}

				closedir( $handle );
			}
		}
	
		return $totalSize;

	}

}
