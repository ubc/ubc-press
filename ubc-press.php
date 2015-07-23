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
	 * Our initialization method which loads the required files and sets any actions
	 * and filters we need.
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

		$ubc_press = new \UBC\Press\Setup;
		$ubc_press->init();

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

}/* class UBC_Press */

// Fire it up
add_action( 'plugins_loaded', 'plugins_loaded_init_ubc_press' );

function plugins_loaded_init_ubc_press() {

	$UBC_Press = new UBC_Press();
	$UBC_Press->init();

}/* plugins_loaded_init_ubc_press() */
