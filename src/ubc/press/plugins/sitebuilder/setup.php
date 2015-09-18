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

		$this->setup_filters();

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

		add_action( 'save_post', array( $this, 'save_post__link_section_with_components' ), 99 );

	}/* setup_actions() */



	/**
	 * Filters to modify items in SiteBuilder
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

		// Change 'Add Widget' to 'Add Component'
		add_filter( 'gettext', array( $this, 'gettext__change_add_widgets' ), 10, 3 );

		// Change 'Page Builder' to 'Content Builder'
		add_filter( 'gettext', array( $this, 'gettext__change_page_builder' ), 10, 3 );

		// Scrub 'Switch to Editor' on Section/Assignment post type
		add_filter( 'gettext', array( $this, 'gettext__change_switch_to_editor' ), 10, 3 );

		// When there's no components, there's a Add a +widget, []row or &prebuilt layout message. Change it
		add_filter( 'gettext', array( $this, 'gettext__change_no_component_message' ), 10, 3 );

	}/* setup_filters() */


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

		// Don't add it for handouts/links
		$exclude = array(
			'handout',
			'link',
		);

		if ( in_array( $cpt_object->post_type_name, $exclude ) ) {
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


	/**
	 * Replace 'widget' with 'component' to stop confusion for users
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $translations - A translations object for this domain
	 * @param (string) $text - The text being translated
	 * @param (string) $domain - The current domain
	 * @return (string) Modified text
	 */

	public function gettext__change_add_widgets( $translations, $text, $domain ) {

		if ( 'siteorigin-panels' !== $domain ) {
			return $translations;
		}

		// Test if $text contains either 'Widget' or 'widget'

		if ( false === strpos( $text, 'widget' ) && false === strpos( $text, 'Widget' ) ) {
			return $translations;
		}

		return str_replace( 'widget', 'component', str_replace( 'Widget', 'Component', $text ) );

	}/* gettext__change_add_widgets() */


	/**
	 * SiteBuilder has an 'Page Builder' title. We want to call it 'Content Builder'
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $translations - A translations object for this domain
	 * @param (string) $text - The text being translated
	 * @param (string) $domain - The current domain
	 * @return (string) Modified text
	 */

	public function gettext__change_page_builder( $translations, $text, $domain ) {

		if ( 'siteorigin-panels' !== $domain ) {
			return $translations;
		}

		if ( 'Page Builder' !== $text ) {
			return $translations;
		}

		return __( 'Content Builder', \UBC\Press::get_text_domain() );

	}/* gettext__change_page_builder() */


	/**
	 * We don't need the 'Switch To Editor' link on the Sections post type
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $translations - A translations object for this domain
	 * @param (string) $text - The text being translated
	 * @param (string) $domain - The current domain
	 * @return (string) Modified text
	 */

	public function gettext__change_switch_to_editor( $translations, $text, $domain ) {

		if ( 'siteorigin-panels' !== $domain ) {
			return $translations;
		}

		if ( 'Switch to Editor' !== $text ) {
			return $translations;
		}

		$current_screen = get_current_screen();

		if ( ! $current_screen || ! is_a( $current_screen, 'WP_Screen' ) ) {
			return $translations;
		}

		$exclude_from = array(
			'section',
			'assignment',
		);

		if ( ! in_array( $current_screen->id, $exclude_from ) ) {
			return $translations;
		}

		return __( '', \UBC\Press::get_text_domain() );

	}/* gettext__change_switch_to_editor() */


	/**
	 * Change the default message shown when there are no components
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $translations - A translations object for this domain
	 * @param (string) $text - The text being translated
	 * @param (string) $domain - The current domain
	 * @return (string) Modified text
	 */

	public function gettext__change_no_component_message( $translations, $text, $domain ) {

		if ( 'siteorigin-panels' !== $domain ) {
			return $translations;
		}

		if ( 'Add a 1{widget}, 2{row} or 3{prebuilt layout} to get started. Read our 4{documentation} if you need help.' !== $text ) {
			return $translations;
		}

		return __( 'Add a 1{component}, 2{row} or 3{prebuilt layout} to get started.', \UBC\Press::get_text_domain() );

	}/* gettext__change_no_component_message() */



	/**
	 * When a section is saved and it contains components from SiteBuilder, we
	 * create a link between the components and the section
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post being saved
	 * @return null
	 */

	public function save_post__link_section_with_components( $post_id ) {

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Ensure it's a section being saved
		if ( 'section' !== get_post_type( $post_id ) ) {
			return;
		}

		// Panels data (components) is stored in post meta ['panels_data']
		$panels_data = get_post_meta( $post_id, 'panels_data', true );

		if ( empty( $panels_data ) || ! isset( $panels_data['widgets'] ) ) {
			return;
		}

		$associations = array();

		foreach ( $panels_data['widgets'] as $id => $panel_widget ) {

			$class = $panel_widget['panels_info']['class'];
			// Determine which type of widget this is
			$widget_type = $this->get_panels_widget_type( $class );
			if ( false === $widget_type ) {
				continue;
			}

			$post_id_from_widget = $this->get_post_id_from_widget( $widget_type, $panel_widget );
			$associations[] = $post_id_from_widget;
		}

		if ( empty( $associations ) ) {
			return;
		}

		// Now, $associations is an array of post IDs which are associated with this section
		foreach ( $associations as $id => $post_id ) {

			$current_associations = get_post_meta( $post_id, 'section_associations', true );
			if ( ! is_array( $current_associations ) ) {
				$current_associations = array();
			}

			$current_associations[] = $post_id;
			update_post_meta( $post_id, 'section_associations', $current_associations );
		}

	}/* save_post__link_section_with_components() */



	/**
	 * Get the widget type from the widget class, allows us to know what to do
	 * or get from the other data
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $class - The class used to generate the widget
	 * @return (string|false) The type of widget, i.e. 'AddAssignmentWidget' or 'AddLinkWidget' or false if it's not a custom UBC Press widget
	 */

	private function get_panels_widget_type( $class = '' ) {

		// List of UBCPress widgets
		$ubc_press_widgets = \UBC\Press\Plugins\SiteBuilder\Widgets\Setup::$registered_ubc_press_widgets;

		foreach ( $ubc_press_widgets as $id => $widget_class ) {
			if ( strpos( $class, $widget_class ) ) {
				return $widget_class;
			}
		}

		return false;

	}/* get_panels_widget_type() */



	/**
	 * Our custom widgets stored a post ID, but in a separate field depending on their type
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $widget_type - The type of widget
	 * @param (array) $panel_widget - the widget we're looking through
	 * @return (int|false) The Post ID associated with the widget, or false if none
	 */

	private function get_post_id_from_widget( $widget_type = '', $panel_widget ) {

		$post_id_key = false;

		switch ( $widget_type ) {

			case 'AddAssignmentWidget':
				$post_id_key = 'assignment_post_id';
				break;

			case 'AddHandoutWidget':
				$post_id_key = 'handout_post_id';
				break;

			case 'AddReadingWidget':
				$post_id_key = 'reading_post_id';
				break;

			case 'AddLinkWidget':
				$post_id_key = 'link_post_id';
				break;

			default:
				break;
		}

		if ( false === $post_id_key ) {
			return false;
		}

		return $panel_widget[ $post_id_key ];

	}/* get_post_id_from_widget() */


}/* class Setup */
