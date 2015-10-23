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

namespace UBC;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Return if this is loaded via WP CLI for now
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}

class Press {

	/**
	 * The version of this plugin
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (string) $version The version of this plugin
	 */

	protected static $version = '1.0.0';


	/**
	 * The text domain for this plugin
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (string) $text_domain The text domain for this plugin
	 */

	protected static $text_domain = 'ubc-press';


	/**
	 * Instance of this class
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (object) $instance
	 */

	protected static $instance;


	/**
	 * The path to this file
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string $plugin_path
	 */

	public static $plugin_path = '';

	/**
	 * The url to this file
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (string) $plugin_url
	 */

	public static $plugin_url = '';


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

		if ( ! is_null( self::$instance ) ) {
			return;
		}

		self::$instance = new self;

		// Set the plugin path as where this file resides
		self::$plugin_path = trailingslashit( dirname( __FILE__ ) );

		// And the URL
		self::$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );

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



	/**
	 * Method run on plugin activation. In order for us to be able to do stuff internally
	 * we need to initialize this class, which calls the autoloader. This means we can
	 * keep our code nice and encapsulated
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function on_activation() {

		self::init();
		do_action( 'ubc_press_on_activation' );

	}/* on_activation() */


	/**
	 * Method run on plugin deactivation
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function on_deactivation() {

		do_action( 'ubc_press_on_deactivation' );

	}/* on_deactivation() */


	/**
	 * A quick getter for the plugin version number
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (int) The version of this plugin
	 */

	public static function get_version() {

		return static::$version;

	}/* get_version() */



	/**
	 * Quick getter for the text domain of this plugin
	 * Usage: \UBC\Press::get_text_domain()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The text domain of this plugin
	 */

	public static function get_text_domain() {

		return static::$text_domain;

	}/* get_text_domain() */


	/**
	 * Quick getter for the plugin path
	 * Usage: \UBC\Press::get_plugin_path()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The path of this plugin
	 */

	public static function get_plugin_path() {

		return static::$plugin_path;

	}/* get_plugin_path() */


	/**
	 * Quick getter for the plugin URL
	 * Usage: \UBC\Press::get_plugin_url()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The url to this plugin root
	 */

	public static function get_plugin_url() {

		return static::$plugin_url;

	}/* get_plugin_url() */

}/* class \UBC\Press */

// Fire it up
add_action( 'plugins_loaded', array( '\UBC\Press', 'init' ) );


// On plugin activation, we do bits and pieces
\register_activation_hook( __FILE__, array( '\UBC\Press', 'on_activation' ) );
\register_deactivation_hook( __FILE__, array( '\UBC\Press', 'on_deactivation' ) );
