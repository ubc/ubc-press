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

		// Set up constants
		self::setup_constants();

		// Custom AJAX Endpoint creation
		self::setup_ajax();

		// Setup plugin tieups
		self::setup_plugins();

		// Set up REST API
		self::setup_rest_api();

		// Set up the dashboard
		self::setup_wp_dashboard();

		// Set up the student front-end dashboard
		self::setup_student_dashboard();

		// Setup our custom taxonomies
		self::setup_cts();

		// Setup our custom post types
		self::setup_cpts();

		// Setup activation/deactivation
		self::setup_activation_deactivation();

		// Setup metaboxes
		self::setup_metaboxes();

		// Set up changes we make for the theme
		self::setup_theme();

		// Setup help text, widgets etc.
		self::setup_help();

		// onboarding
		self::setup_onboarding();

		// Setup Widgets
		self::setup_widgets();

	}/* init() */



	/**
	 * Set up the set of constants we use. These do various things, such as let
	 * us know the plugin is active
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_constants() {

		if ( ! defined( 'UBC_PRESS_VERSION' ) ) {
			define( 'UBC_PRESS_VERSION', \UBC\Press::get_version() );
		}

	}/* setup_constants() */


	/**
	 * The default WordPress dashboard isn't great for a T&L Environment. Let's make a few changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_wp_dashboard() {

		$wp_dashboard = new \UBC\Press\WPDashboard\Setup;
		$wp_dashboard->init();

	}/* setup_wp_dashboard() */



	/**
	 * Our front-end student dashboard
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_student_dashboard() {

		if ( is_admin() ) {
			return;
		}

		$student_dashboard = new \UBC\Press\StudentDashboard\Setup;
		$student_dashboard->init();

	}/* setup_student_dashboard() */


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
	 * Extra bits and pieces for plugins we support
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_plugins() {

		$plugins = new \UBC\Press\Plugins\Setup;
		$plugins->init();

	}/* setup_plugins() */


	/**
	 * Set up our theme changes. Mainly changing order/layout
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_theme() {

		$theme = new \UBC\Press\Theme\Setup;
		$theme->init();

	}/* setup_theme() */



	/**
	 * Set up the class which deals with all of the help text we have across
	 * various screens
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_help() {

		$help = new \UBC\Press\Help\Setup;
		$help->init();

	}/* setup_help() */


	/**
	 * Set up the class which deals with all of the onboarding procedures
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_onboarding() {

		$onboarding = new \UBC\Press\Onboarding\Setup;
		$onboarding->init();

	}/* setup_onboarding() */

	/**
	 * Set up the widgets
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_widgets() {

		$widgets = new \UBC\Press\Widgets\Setup;
		$widgets->init();

	}/* setup_widgets() */


	/**
	 * Set up the custom AJAX endpoints creation.
	 *
	 * This enables us to not use admin-ajax from the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_ajax() {

		$ajax = new \UBC\Press\Ajax\Setup;
		$ajax->init();

	}/* setup_ajax() */


	/**
	 * WP REST API Tie-ups
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_rest_api() {

		$api = new \UBC\Press\API\Setup;
		$api-> init();

	}/* setup_rest_api() */


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
