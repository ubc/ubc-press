<?php

namespace UBC\Press\Plugins\SiteBuilder;

/**
 * Setup for our SiteBuilder plugin mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage SiteBuilder
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

		// When a section is saved, make a link between it and its components
		add_action( 'save_post', array( $this, 'save_post__link_section_with_components' ), 99 );

		// Fill content of components admin column
		add_action( 'manage_pages_custom_column', array( $this, 'manage_pages_custom_column__section_components_content' ), 10, 2 );

		// Display section links on components listing screen
		add_action( 'manage_assignment_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );
		add_action( 'manage_handout_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );
		add_action( 'manage_note_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );
		add_action( 'manage_reading_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );
		add_action( 'manage_link_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );
		add_action( 'manage_post_posts_custom_column', array( $this, 'manage_component_posts_custom_column__component_sections_content' ), 10, 2 );

		// Output a 'mark as complete' button before the components
		add_action( 'ubc_press_show_template_for_post_of_post_type_before', array( $this, 'ubc_press_show_template_for_post_of_post_type_after__add_mark_as_complete' ), 10, 4 );

		// Our custom AJAX implementation gives us ubcpressajax_mark_component_complete
		add_action( 'ubcpressajax_mark_as_complete', array( $this, 'ubcpressajax_mark_as_complete__process' ) );

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

		// Section admin column to add components
		add_filter( 'manage_section_posts_columns' , array( $this, 'manage_section_posts_columns__add_components' ) );

		// Componenents listing screen, show attached section
		add_filter( 'manage_assignment_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );
		add_filter( 'manage_handout_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );
		add_filter( 'manage_note_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );
		add_filter( 'manage_reading_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );
		add_filter( 'manage_link_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );
		add_filter( 'manage_post_posts_columns' , array( $this, 'manage_assignment_posts_columns__add_section' ) );

		// Modify the tabs in the 'Add New Component' overlay
		add_filter( 'siteorigin_panels_widget_dialog_tabs', array( $this, 'siteorigin_panels_widget_dialog_tabs__remove_unused_tabs' ), 100 );

		// Modify the list of widgets shown in the Add New Component overlay
		add_filter( 'siteorigin_panels_widgets', array( $this, 'siteorigin_panels_widgets__remove_unused_widgets' ), 100 );

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

		// Don't add it for handouts/links/assignments
		$exclude = array(
			'handout',
			'link',
			'assignment',
			'submission',
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

			$class = ( isset( $panel_widget['panels_info'] ) && isset( $panel_widget['panels_info']['class'] ) ) ? $panel_widget['panels_info']['class'] : false;
			// Determine which type of widget this is
			$widget_type = \UBC\Press\Utils::get_panels_widget_type( $class );
			if ( false === $widget_type ) {
				continue;
			}

			$post_id_from_widget = \UBC\Press\Utils::get_post_id_from_widget( $widget_type, $panel_widget );
			$associations[] = $post_id_from_widget;
		}

		if ( empty( $associations ) ) {

			// If $associations is empty on save, we need to check if there were associations initially. If so,
			// they need to be removed
			$original_associations = get_post_meta( $post_id, 'component_associations', true );

			// None originally? Cool, we're done here
			if ( ! $original_associations || empty( $original_associations ) ) {
				return;
			}

			// There were some, so let's set them and then deal with them
			foreach ( $original_associations as $okey => $o_post_id ) {

				// First remove it from the component
				$components_section_assocations = get_post_meta( $o_post_id, 'section_associations', true );

				if ( empty( $components_section_assocations ) ) {
					continue;
				}

				// Loop over each of this component's section associations looking for the just saved section ID
				// Then remove it from the array and update the component
				foreach ( $components_section_assocations as $c_s_a_k => $c_s_a_post_id ) {
					if ( $post_id !== $c_s_a_post_id ) {
						continue;
					}
					unset( $components_section_assocations[ $c_s_a_k ] );
				}

				update_post_meta( $o_post_id, 'section_associations', $components_section_assocations );
			}
		}

		// Now, $associations is an array of post IDs (of components) which are associated with this section
		foreach ( $associations as $id => $component_post_id ) {

			$current_associations = get_post_meta( $component_post_id, 'section_associations', true );
			if ( ! is_array( $current_associations ) ) {
				$current_associations = array();
			}

			// Add post meta to the component saying it's associated with this section just been saved
			if ( ! in_array( $post_id, $current_associations ) ) {
				$current_associations[] = $post_id;
			}
			update_post_meta( $component_post_id, 'section_associations', $current_associations );
		}

		// Now mark associations within this post too and remove associations no longer present
		$section_associations = get_post_meta( $post_id, 'component_associations', true );

		if ( ! is_array( $section_associations ) ) {
			// No need to delete anything as there aren't any set
			// so just add the new ones
			update_post_meta( $post_id, 'component_associations', $associations );
			return;
		}

		// There already are associations made on this section, so we need to check
		// if any of the *existing* associations are NOT in the list that's just been
		// saved, if so, we need to remove that association from the component
		foreach ( $section_associations as $key => $section_assoc_post_id ) {
			if ( ! in_array( $section_assoc_post_id, $associations ) ) {
				// Remove from the component
				$component_assoc = get_post_meta( $section_assoc_post_id, 'section_associations', true );
				if ( empty( $component_assoc ) ) {
					continue;
				}
				$key_on_comp = array_search( $section_assoc_post_id, $component_assoc );
				unset( $component_assoc[ $key_on_comp ] );
				update_post_meta( $section_assoc_post_id, 'section_associations', $component_assoc );
			}
		}

		// Now overwrite the existing component_associations in the section's postmeta with what's just been saved
		update_post_meta( $post_id, 'component_associations', $associations );

	}/* save_post__link_section_with_components() */


	/**
	 * Add a components column to the sections listing
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $columns - Preset columns
	 * @return (array) Modified columns
	 */

	public function manage_section_posts_columns__add_components( $columns ) {

		$columns = array_slice( $columns, 0, 2, true ) + array( 'components' => __( 'Components', \UBC\Press::get_text_domain() ) ) + array_slice( $columns, 2, count( $columns ) - 1, true );

		return $columns;

	}/* manage_section_posts_columns__add_components() */



	/**
	 * Fill the content of the components admin column. This fetches the components
	 * added via the SiteBuilder
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $column - Which column are we adding content to
	 * @param (int) $post_id - the ID for the post for each row
	 * @return null
	 */

	public function manage_pages_custom_column__section_components_content( $column, $post_id ) {

		switch ( $column ) {

			case 'components':

				$panel_meta = get_post_meta( $post_id, 'panels_data', true );

				// No panels or no widgets
				if ( empty( $panel_meta ) || ! isset( $panel_meta['widgets'] ) || empty( $panel_meta['widgets'] ) ) {
					echo esc_html__( 'No Components', \UBC\Press::get_text_domain() );
					return;
				}

				$widgets = $panel_meta['widgets'];

				// Need to get the component type. If it's one of ours, link to it.
				foreach ( $widgets as $id => $widget_data ) {

					$widget_type = \UBC\Press\Utils::get_panels_widget_type( $widget_data['panels_info']['class'] );
					$component_column_content = $this->build_component_column_content( $widget_type, $widget_data );

					echo wp_kses_post( $component_column_content ) . '<br />';

				}

			break;

		}

	}/* manage_pages_custom_column__section_components_content() */



	/**
	 * Build the content for the Components panel for Sections
	 * If it's a linkable component, we link to it. Prepend the compoent
	 * with the type, i.e. Lecture: Lecture 1
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $widget_type - What type of SO Panel widget is it (the class)
	 * @param (array) $widget_data - The full set of widget data for this widget
	 * @return (string) The content for the column
	 */

	public function build_component_column_content( $widget_type, $widget_data ) {

		$widget_nicename	= $this->get_panels_widget_nice_name( $widget_type );

		// See if this component has a linkable ID
		$component_post_id	= \UBC\Press\Utils::get_post_id_from_widget( $widget_type, $widget_data );

		// Start fresh
		$content = '';

		// Always prepend with the type
		$content .= $widget_nicename;

		if ( $component_post_id && isset( $widget_data['text'] ) ) {
			$component_permalink = get_permalink( $component_post_id );
			$content .= ': <a href="' . esc_url( $component_permalink ) . '" title="' . esc_html( $widget_data['text'] ) . '">' . esc_html( $widget_data['text'] ) . '</a>';
		}

		return $content;

	}/* build_component_column_content() */


	/**
	 * Get the nice name of a panels widget
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $class - The class name
	 * @return (string) The nice name
	 */

	public function get_panels_widget_nice_name( $class = '' ) {

		$nice_name = false;

		switch ( $class ) {

			case 'AddQuizWidget':
				$nice_name = __( 'Quiz', \UBC\Press::get_text_domain() );
				break;
			case 'AddLectureWidget':
				$nice_name = __( 'Lecture', \UBC\Press::get_text_domain() );
				break;
			case 'AddAssignmentWidget':
				$nice_name = __( 'Assignment', \UBC\Press::get_text_domain() );
				break;
			case 'AddReadingWidget':
				$nice_name = __( 'Reading', \UBC\Press::get_text_domain() );
				break;
			case 'AddLinkWidget':
				$nice_name = __( 'Link', \UBC\Press::get_text_domain() );
				break;
			case 'AddDiscussionForumWidget':
				$nice_name = __( 'Forum', \UBC\Press::get_text_domain() );
				break;
			case 'AddHandoutWidget':
				$nice_name = __( 'Handout', \UBC\Press::get_text_domain() );
				break;

			case 'WP_Widget_Black_Studio_TinyMCE':
				$nice_name = __( 'Text', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_Editor_Widget':
				$nice_name = __( 'Text', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_GoogleMap_Widget':
				$nice_name = __( 'Map', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_Button_Widget':
				$nice_name = __( 'Button', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_Features_Widget':
				$nice_name = __( 'Feature', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_Image_Widget':
				$nice_name = __( 'Image', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_PostCarousel_Widget':
				$nice_name = __( 'Posts', \UBC\Press::get_text_domain() );
				break;

			case 'SiteOrigin_Widget_Slider_Widget':
				$nice_name = __( 'Slider', \UBC\Press::get_text_domain() );
				break;

			default:

				break;

		}

		return $nice_name;

	}/* get_panels_widget_nice_name() */


	/**
	 * Fill the content of the section admin column. This fetches the attached
	 * sections for the component (stored as post meta)
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $column - Which column are we adding content to
	 * @param (int) $post_id - the ID for the post for each row
	 * @return null
	 */
	public function manage_component_posts_custom_column__component_sections_content( $column, $post_id ) {

		switch ( $column ) {

			case 'sections':

				$section_associations = get_post_meta( $post_id, 'section_associations', true );

				if ( ! $section_associations || empty( $section_associations ) ) {
					echo esc_html__( 'No associated sections', \UBC\Press::get_text_domain() );
					return;
				}

				foreach ( $section_associations as $key => $section_post_id ) {
					$title = get_the_title( $section_post_id );
					$permalink = get_permalink( $section_post_id );
					echo wp_kses_post( '<p><a href="' . $permalink . '" title="' . $title . '">' . $title . '</a></p>' );
				}

			break;

		}

	}/* manage_component_posts_custom_column__component_sections_content() */

	/**
	 * Add a components column to the sections listing
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $columns - Preset columns
	 * @return (array) Modified columns
	 */

	public function manage_assignment_posts_columns__add_section( $columns ) {

		$columns = array_slice( $columns, 0, 2, true ) + array( 'sections' => __( 'Sections', \UBC\Press::get_text_domain() ) ) + array_slice( $columns, 2, count( $columns ) - 1, true );

		return $columns;

	}/* manage_assignment_posts_columns__add_section() */


	/**
	 * Add a mark as complete button to each component
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $template_start -
	 * @param (string) $template_path -
	 * @param (int) $post_id -
	 * @param (string) $post_type -
	 * @return null
	 */

	public function ubc_press_show_template_for_post_of_post_type_after__add_mark_as_complete( $template_start, $template_path, $post_id, $post_type ) {

		// Sanitize
		$post_id	= absint( $post_id );
		$post_type	= sanitize_text_field( $post_type );

		// Determine if this is a component that can be completed or not
		if ( ! \UBC\Press\Utils::component_can_be_completed( $post_id ) ) {
			return;
		}

		// Quizzes are special. They are completeable, but not manually. Completed automatically when quiz is finished.
		if ( \UBC\Press\Utils::component_is_completed_automatically( $post_id ) ) {
			return;
		}

		// Start fresh with data
		$data = array();

		// Determine if we're marking as complete or incomplete
		$user_id	= get_current_user_id();
		$completed	= \UBC\Press\Utils::component_is_completed( $post_id, $user_id );

		$data['post_id']	= $post_id;
		$data['post_type']	= $post_type;
		$data['completed']	= $completed;

		// If completed, we'll also send the timestamp
		if ( $completed ) {
			$data['when_completed'] = \UBC\Press\Utils::get_when_component_was_completed( $post_id, $user_id );
		}

		\UBC\Helpers::locate_template_part_in_plugin( \UBC\Press::get_plugin_path() . 'src/ubc/press/theme/templates/', 'mark-as-complete.php', true, false, $data );

	}/* ubc_press_show_template_for_post_of_post_type_after__add_mark_as_complete() */


	/**
	 * Handle the custom AJAX call to make a component as complete. Must send either
	 * wp_send_json_success or wp_send_json_failure, or if neither *must* use exit; at end
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $request_data - the $_REQUEST data
	 * @return null
	 */

	public function ubcpressajax_mark_as_complete__process( $request_data ) {

		// The nonce is already checked for us, still need to sanitize data
		$post_id = absint( $request_data['post_id'] );
		$user_id = get_current_user_id();

		$is_completed = \UBC\Press\Utils::component_is_completed( $post_id, $user_id );

		if ( true === $is_completed ) {
			$complete = \UBC\Press\Utils::set_component_as_incomplete( $post_id, $user_id );
		} else {
			$complete = \UBC\Press\Utils::set_component_as_complete( $post_id, $user_id );
		}

		// If we're coming from an AJAX request, send JSON
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

			if ( false === (bool) $complete ) {
				wp_send_json_error( array( 'message' => $complete ) );
			}

			wp_send_json_success( array( 'completed' => ! $is_completed ) );

		} else {

			$redirect_to = ( isset( $request_data['redirect_to'] ) ) ? esc_url( $request_data['redirect_to'] ) : false;

			// Otherwise, something went wrong somewhere, but we should not show a whitescreen, so redirect back to the component
			if ( false !== $redirect_to ) {
				header( 'Location: ' . $redirect_to );
			} else {
				header( 'Location: ' . get_permalink( $post_id ) );
			}
		}

	}/* ubcpressajax_mark_as_complete__process() */


	/**
	 * The Add New Component overlay has a looooot of tabs. We don't want a billion.
	 * That's too confusing. We'll remove all of them for now, but perhaps in the
	 * future may add some more specific ones
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $tabs - The currently defined tabs
	 * @return (array) Modified tabs
	 */

	public function siteorigin_panels_widget_dialog_tabs__remove_unused_tabs( $tabs ) {

		$to_remove = array(
			'widgets_bundle',
			'page_builder',
			'wordpress',
			'bbpress',
			'recommended',
		);

		foreach ( $to_remove as $id => $remove ) {
			if ( ! in_array( $remove, array_keys( $tabs ) ) ) {
				continue;
			}

			unset( $tabs[ $remove ] );
		}

		return $tabs;

	}/* siteorigin_panels_widget_dialog_tabs__remove_unused_tabs() */


	/**
	 * Remove unused widgets from the overlay
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $widgets - The widgets currently shown
	 * @return (array) Modified widgets list
	 */

	public function siteorigin_panels_widgets__remove_unused_widgets( $widgets ) {

		$to_remove = array(
			'BBP_Login_Widget',
			'BBP_Views_Widget',
			'BBP_Search_Widget',
			'BBP_Forums_Widget',
			'BBP_Topics_Widget',
			'BBP_Replies_Widget',
			'BBP_Stats_Widget',
			'WP_Widget_Calendar',
			'WP_Widget_Meta',
			'WP_Widget_Search',
		);

		foreach ( $to_remove as $id => $remove ) {

			if ( ! in_array( $remove, array_keys( $widgets ) ) ) {
				continue;
			}

			unset( $widgets[ $remove ] );
		}

		return $widgets;

	}/* siteorigin_panels_widgets__remove_unused_widgets() */

}/* class Setup */
