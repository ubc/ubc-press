<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       UBC Press
 * Plugin URI:        http://ctlt.ubc.ca/
 * Description:       A plugin to help the WP dashboard look and feel more like a teaching and learning platform
 * Version:           1.0.0
 * Author:            Richard Tape
 * Author URI:        http://blogs.ubc.ca/mbcx9rvt
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ubc-press
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Return if this is loaded via WP CLI for now
if ( defined( 'WP_CLI' ) and WP_CLI ) {
	return;
}

class UBC_Press {

	/**
	 * The path to this file
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string $plugin_path
	 */

	public static $plugin_path = '';


	/**
	 * Array of loader files to include in out init, relative to $plugin_path
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string $loader_files
	 */

	public static $loader_files = array(
		'dashboard-widgets/load-dashboard-widgets.php',
		'post-types/load-post-types.php',
		'taxonomies/load-taxonomies.php',
		'roles/load-roles.php',
	);


	/**
	 * Our initialization method which loads the required files and sets any actions
	 * and filters we need. No autoloader just yet.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function init() {

		// Set the plugin path as where this file resides
		self::$plugin_path = trailingslashit( dirname( __FILE__ ) );

		// We have an autoloader for components
		self::load_autoloader();

		// Load the loaders
		add_action( 'init', array( __CLASS__, 'load_loaders' ), 5 );

	}/* init() */


	/**
	 * Require the composer autoloader above all else
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function load_autoloader() {

		require self::$plugin_path . 'vendor/autoload.php';

	}/* load_autoloader() */


	/**
	 * Loads the required loader files for each of our modules
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function load_loaders() {

		/**
		 * Filters the files loaded
		 *
		 * Allows other plugins or options to determine which files are required
		 *
		 * @since 1.0.0
		 *
		 * @param array $loader_files The relative (to this file) paths to the loader files
		 */

		$loaders = apply_filters( 'ubc_press_loader_files', static::$loader_files );

		// Bail if we don't have any
		if ( ! $loaders || ! is_array( $loaders ) || empty( $loaders ) ) {
			return;
		}

		// Loop over and require the files we need
		foreach ( $loaders as $key => $loader ) {

			$actual_path = self::$plugin_path . 'inc/' . $loader;

			if ( ! file_exists( $actual_path ) ) {
				continue;
			}

			require_once $actual_path;

		}

	}/* load_loaders() */


}/* class UBC_Press */

// Fire it up
add_action( 'plugins_loaded', 'plugins_loaded_init_ubc_press' );

function plugins_loaded_init_ubc_press() {

	$UBC_Press = new UBC_Press();
	$UBC_Press->init();

}/* plugins_loaded_init_ubc_press() */
