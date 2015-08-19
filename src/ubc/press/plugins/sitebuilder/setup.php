<?php

namespace UBC\Press\Plugins\SiteBuilder;

/**
 * Setup for our custom meta boxes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Metaboxes
 *
 */


class Setup {

	/**
	 * Path to this plugin's assets
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (str)
	 */

	public static $assets_path;


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		$this->setup_actions();

	}/* init() */

	/**
	 * Add our action hooks
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// Load our JS/CSS
		add_action( 'init', array( $this, 'init__load_plugin_assets' ) );

		// When we register a CPT, we hook in and add it to the 'siteorigin_panels_post_types' option
		add_action( 'ubc_press_after_create_cpt', array( $this, 'ubc_press_after_create_cpt__add_panel_support' ), 10, 4 );

		// load our JS when we need
		add_action( 'current_screen', array( $this, 'current_screen__load_js' ) );

		// Register our custom fields and widgets
		add_action( 'init', array( $this, 'init__register_custom_fields' ), 0 );
		add_action( 'init', array( $this, 'init__register_custom_widgets' ), 0 );

	}/* setup_actions() */


	/**
	 * Adjust the siteorigin_panels_settings option to include this custom post type by default
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $label_args - An array of label arguments for register_post_type
	 * @param (array) $wp_args - An array of CPT WordPress args for registeR_post_type
	 * @param (string) $icon - A name of a dashicons icon
	 * @param (object) $cpt_object - The full, registered CPT object
	 * @return null
	 */

	public function ubc_press_after_create_cpt__add_panel_support( $label_args, $wp_args, $icon, $cpt_object ) {

		if ( ! is_a( $cpt_object, 'CPT' ) || ! isset( $cpt_object->post_type_name ) ) {
			return;
		}

		// Fetch current, or default to post/page
		$all_sp_settings	= get_option( 'siteorigin_panels_settings', array() );
		$post_types 		= ( isset( $all_sp_settings['post-types'] ) ) ? $all_sp_settings['post-types'] : array( 'post', 'page' );

		// If it's already there, bail
		if ( in_array( $cpt_object->post_type_name, $post_types ) ) {
			return;
		}

		// Get the name of the post type just registered
		$post_types[] = $cpt_object->post_type_name;

		// Add it to the settings
		$all_sp_settings['post-types'] = $post_types;

		// Update
		update_option( 'siteorigin_panels_settings', $all_sp_settings );

	}/* ubc_press_after_create_cpt__add_panel_support() */



	/**
	 * Register our CSS/JS
	 *
	 * @since 1.0.0
	 *
	 * @todo Make this class an extension of a base class which has a method to load JS/CSS
	 * @param null
	 * @return null
	 */

	public function init__load_plugin_assets() {

		static::$assets_path = \UBC\Press::$plugin_url . 'src/ubc/press/plugins/sitebuilder/assets/';

		$this->register_js();

		$this->register_css();

	}/* init__load_plugin_assets() */


	/**
	 * Register this plugin's js
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function register_js() {

		wp_register_script( 'ubc-press-plugins-sitebuilder', static::$assets_path . 'js/ubc-press-plugins-sitebuilder.js', array( 'jquery' ), null, true );

	}/* register_js() */



	/**
	 * Conditionally load our JS
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function current_screen__load_js() {

		$all_sp_settings	= get_option( 'siteorigin_panels_settings', array() );
		$post_types 		= ( isset( $all_sp_settings['post-types'] ) ) ? $all_sp_settings['post-types'] : array( 'post', 'page' );
		$screen = \get_current_screen();

		if ( ! is_a( $screen, 'WP_Screen' ) || ! in_array( $screen->post_type, $post_types ) ) {
			return;
		}

		wp_enqueue_script( 'ubc-press-plugins-sitebuilder' );

	}/* current_screen__load_js() */



	/**
	 * Register this plugin's css
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function register_css() {

	}/* register_css() */



	/**
	 * Register our custom Site Origin Page Builder Fields
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__register_custom_fields() {

		$newsofields = new \UBC\Press\Plugins\SiteBuilder\Fields\Setup;
		$newsofields->init();

	}/* init__register_custom_fields() */



	/**
	 * Register custom SO Widgets
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__register_custom_widgets() {

		$newsowidgets = new \UBC\Press\Plugins\SiteBuilder\Widgets\Setup;
		$newsowidgets->init();

	}/* init__register_custom_widgets() */


}/* class Setup */
