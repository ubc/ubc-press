<?php

namespace UBC\Press;

class Setup {

	/**
	 * The instance of this class. Just to ensure we're not set up more than once
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (object) $instance
	 */

	protected static $instance;


	/**
	 * Run our setup routine. We instantiate our custom post types, taxonomies, roles etc.
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

		// Set up the dashboard
		self::setup_dashboard();

		// Setup our custom post types
		self::setup_cpts();

		// Setup our custom taxonomies
		self::setup_cts();

		// Setup activation/deactivation
		self::setup_activation_deactivation();

		// Setup metaboxes
		self::setup_metaboxes();

	}/* init() */


	/**
	 * The default dashboard isn't great for a T&L Environment. Let's make a few changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_dashboard() {

		if ( ! is_admin() ) {
			return;
		}

		$dashboard = new \UBC\Press\Dashboard\Setup;
		$dashboard->init();

	}/* setup_dashboard() */


	/**
	 * Set up our custom post types
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_cpts() {

		// Set up post types
		$post_types = new \UBC\Press\CPTs\Setup;
		$post_types->init();

	}/* cpts() */


	/**
	 * Setup our custom taxonomies
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_cts() {

		// Set up post types
		$taxonomies = new \UBC\Press\CTs\Setup;
		$taxonomies->init();

	}/* setup_cts */



	/**
	 * Set up our custom metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_metaboxes() {

		$metaboxes = new \UBC\Press\Metaboxes\Setup;
		$metaboxes->init();

	}/* setup_metaboxes() */


	/**
	 * Actions and filters to be hooked into during setup
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_activation_deactivation() {

		// Fired on plugin activation
		add_action( 'ubc_press_on_activation', array( __CLASS__, 'on_activation' ) );

		// Fired on plugin deactivation
		add_action( 'ubc_press_on_deactivation', array( __CLASS__, 'on_deactivation' ) );

	}/* setup_actions() */


	/**
	 * This method encapsulates the stuff we do on plugin activation. It's called from the
	 * main plugin file
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function on_activation() {

		// Just to be sure, check the user can do this
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

		// And also check we're coming from the right place
		check_admin_referer( "activate-plugin_{$plugin}" );

		// Set up our roles
		$roles = new \UBC\Press\Roles\Setup;
		$roles->init();

	}/* on_activation() */



	/**
	 * Run when the plugin is deactivated. Again, called from the main plugin file
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function on_deactivation() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;

		}

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

		check_admin_referer( "deactivate-plugin_{$plugin}" );

	}/* on_deactivation() */


}/* class setup */
