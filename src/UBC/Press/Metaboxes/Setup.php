<?php

namespace UBC\Press\Metaboxes;

/**
 * Setup for our custom meta boxes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Metaboxes
 *
 */


class Setup extends \UBC\Press\ActionsBeforeAndAfter {

	/**
	 * The metaboxes which we will create
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (array) $metaboxes
	 */

	public static $metaboxes_to_create = array();


	/**
	 * Our initializer which determines and then creates our custom meta boxes
	 * Also runs methods before and after creation which run actions enabling us
	 * to hook in if required
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Run an action so we can hook in beforehand
		$this->before();

		// Determine which metaboxes to create
		$this->determine();

		// Create the metaboxes
		$this->create();

		// Run an action so we can hook in afterwards
		$this->after();

	}/* init() */


	/**
	 * Determine which metaboxes to create
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function determine() {

	}/* determine() */


	/**
	 * Create the actual metaboxes based on what has been determine()'d
	 * The init file within CMB2 is autoloaded using composer
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function create() {

		// Load custom admin metaboxes JS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts__load_admin_metaboxes_js' ) );

		// Add a section description metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__section_description' ) );

		// Add a handout details metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__handout_details' ) );

		// Add a URL link to the links post type
		add_action( 'cmb2_init', array( $this, 'cmb2_init__link_details' ) );

		// Course settings metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__course_settings' ) );

		// Add a date metabox to some post types
		add_action( 'cmb2_init', array( $this, 'cmb2_init__date' ) );

		// Assignments have a slightly adapted date/time
		add_action( 'cmb2_init', array( $this, 'cmb2_init__assignment_date' ) );

		// This is the front-end notes submission form
		add_action( 'cmb2_init', array( $this, 'cmb2_init__user_notes' ) );

		// Save user notes
		add_action( 'cmb2_init', array( $this, 'cmb2_init__save_user_notes' ) );
		add_action( 'ubcpressajax_user_notes', array( $this, 'ubcpressajax_user_notes__process' ) );

		// The form we need for user onboarding
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_admin_init__onboarding' ) );

		// The metabox which combines network level program objectives and course objectives taxonomies
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_admin_init__program_course_objectives' ) );

		// add_action( 'cmb2_init', array( $this, 'cmb2_init__test' ) );
		// When a post is saved that contains the date/time, we need to save the date as a hidden timestamp
		add_action( 'save_post', array( $this, 'save_post__save_hidden_timestamp' ), 100, 2 );
		add_action( 'publish_post', array( $this, 'save_post__save_hidden_timestamp' ), 100, 2 );

		add_action( 'cmb2_admin_init', array( $this, 'cmb2_admin_init__save_onboarding_options' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes__subsection_icon' ) );
		add_action( 'save_post', array( $this, 'save_post__save_icon_metabox' ) );

		// Assignment's "create new assignment form" metabox
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_admin_init__show_assignment_form_create_form_markup' ) );
		add_action( 'ubcpressajax_create_assignment_form', array( $this, 'ubcpressajax_create_assignment_form__process' ) );
		add_action( 'gform_after_submission', array( $this, 'gform_after_submission__create_submission_for_assignments' ), 10, 2 );
		add_filter( 'gform_disable_post_creation', array( $this, 'gform_disable_post_creation__stop_auto_post_creation_for_assignments' ), 10, 3 );

	}/* create() */


	public function admin_enqueue_scripts__load_admin_metaboxes_js() {

		// back-end only
		if ( ! is_admin() ) {
			return;
		}

		wp_register_script( 'ubc_press_admin_metaboxes', \UBC\Press::get_plugin_url() . 'src/UBC/Press/Metaboxes/assets/js/ubc-press-admin-metaboxes.js', array( 'jquery' ), null, true );

		$localized_data = array(
			'ajax_url'	=> \UBC\Press\Ajax\Utils::get_ubc_press_ajax_url(),
			'text'		=> array(
				'loading' => __( 'Loading', \UBC\Press::get_text_domain() ),
				'completed' => __( 'Completed', \UBC\Press::get_text_domain() ),
				'please_correct' => __( 'Please correct the highlighted fields.', \UBC\Press::get_text_domain() ),
			),
		);

		wp_localize_script( 'ubc_press_admin_metaboxes', 'ubc_press_admin_metaboxes_vars', $localized_data );

		wp_enqueue_script( 'ubc_press_admin_metaboxes' );

	}/* admin_enqueue_scripts__load_admin_metaboxes_js */


	/**
	 * A section requires a description.
	 *
	 * @since 1.0.0
	 *
	 * @param  null
	 * @return null
	 */

	public function cmb2_init__section_description() {

		$prefix = '_section_description_';

		// Create the metabox
		$section_description = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Section Details', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'section' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
		) );

		// Add fields to the metabox
		$section_help = $section_description->add_field( array(
			'name' => __( 'Where are section details displayed?', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'title_info',
			'desc' => __( 'Section details are shown on the listings page for the course (which may include the course home page).', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );

		$section_description_content = $section_description->add_field( array(
			'name'    => __( '', \UBC\Press::get_text_domain() ),
			'desc'	  => __( 'Give a brief (20-30 word) description of the content students will find in this course section. Perhaps an overview of the content within each component.', \UBC\Press::get_text_domain() ),
			'id'      => $prefix . 'content',
			'type'    => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 8,
				'media_buttons' => false,
				'teeny' => true,
			),
		) );

		if ( ! is_admin() || ! class_exists( '\Cmb2Grid\Grid\Cmb2Grid' ) ) {
			return;
		}
		$grid_layout = new \Cmb2Grid\Grid\Cmb2Grid( $section_description );
		$row_1 = $grid_layout->addRow();
		$row_1->addColumns( array( $section_help, $section_description_content ) );

	}/* cmb2_init__section_description() */


	/**
	 * Add a handout details metabox for the handout CPT which allows an instructor
	 * to upload a file and a description of the file as well as choose an icon
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__handout_details() {

		$prefix = '_handout_details_';

		// Create the metabox
		$handout_details = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Handout Details', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'handout' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
			'show_names'	=> true,
		) );

		$handout_media = $handout_details->add_field( array(
			'name'         => __( 'Handout Files', \UBC\Press::get_text_domain() ),
			'desc'         => __( 'Upload or add multiple images/attachments.', \UBC\Press::get_text_domain() ),
			'id'           => $prefix . 'file_list',
			'type'         => 'file_list',
		) );

		$handout_description = $handout_details->add_field( array(
			'name' => __( 'Handout Description', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'description',
			'type' => 'textarea',
			'desc' => __( 'A brief description of the handout, perhaps the file type, size or contents of a zip file.', \UBC\Press::get_text_domain() ),
		) );

	}/* cmb2_init__handout_details() */


	/**
	 * Link details - URL
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__link_details() {

		$prefix = '_link_details_';

		// Create the metabox
		$link_details = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Link Details', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'link' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
			'show_names'	=> true,
		) );

		$link_details_group = $link_details->add_field( array(
			'id'          => $prefix . 'link_details_group',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => __( 'Link {#}', \UBC\Press::get_text_domain() ),
				'add_button'    => __( 'Add Another Link', \UBC\Press::get_text_domain() ),
				'remove_button' => __( 'Remove Link', \UBC\Press::get_text_domain() ),
				'sortable'      => true, // beta
			),
		) );

		$link_url = $link_details->add_group_field( $link_details_group, array(
			'name'			=> __( 'Link URL(s)', \UBC\Press::get_text_domain() ),
			'id'			=> $prefix . 'link_list',
			'type' 			=> 'text_url',
			'repeatable' 	=> true,
			'options' => array(
				'add_row_text' => __( 'Add URL', \UBC\Press::get_text_domain() ),
			),
		) );

		$link_description = $link_details->add_group_field( $link_details_group, array(
			'name'			=> __( 'Link Description', \UBC\Press::get_text_domain() ),
			'id'			=> $prefix . 'link__description',
			'type' 			=> 'textarea',
		) );

	}/* cmb2_init__link_details() */


	/**
	 * Course Settings
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__course_settings() {

		$prefix = 'ubc_course_settings_';

		$course_settings = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Course Settings', \UBC\Press::get_text_domain() ),
			'show_on'		=> array(
				'key'   	=> 'options-page',
				'value' 	=> array( 'ubc_course_settings' ),
			),
			'cmb_styles' 	=> false,
			'hookup' 		=> false,
		) );

		$course_settings->add_field( array(
			'name'       => __( 'Course Code', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'i.e. Arts101 or PHYS305d', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'course_code',
			'type'       => 'text',
		) );

		$course_settings->add_field( array(
			'name'             => __( 'Faculty', \UBC\Press::get_text_domain() ),
			'id'               => $prefix . 'faculty',
			'type'             => 'select',
			'show_option_none' => true,
			'options'          => \UBC\Press\Utils::get_faculty_list(),
		) );

		$departments = \UBC\Press\Utils::get_department_list();

		if ( ! empty( $departments ) ) {

			foreach ( $departments as $faculty => $departments ) {
				$course_settings->add_field( array(
					'name'             => __( 'Department', \UBC\Press::get_text_domain() ),
					'id'               => $prefix . 'department_' . $faculty,
					'type'             => 'select',
					'show_option_none' => true,
					'options'          => $departments,
				) );
			}
		}

	}/* cmb2_init__course_settings() */


	/**
	 * We have dates for posts like Lectures, Assignments etc.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__date() {

		$prefix = 'ubc_item_date_';

		$item_date = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Date/Time', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'lecture' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
			'show_names'	=> false,
		) );

		$title = $item_date->add_field( array(
			'name' => __( '', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'date_title',
			'desc' => __( 'Adding a date and time will add this item to the calendar automatically.', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );

		$text_date = $item_date->add_field( array(
			'name'       => __( 'Date', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. MM/DD/YYYY', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_date',
			'type'       => 'text_date',
		) );

		$hidden_timestamp = $item_date->add_field( array(
			'name'       => __( 'Hidden Timestamp', \UBC\Press::get_text_domain() ),
			'desc'       => __( '', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'hidden_timestamp',
			'type'       => 'hidden',
		) );

		$text_time_start = $item_date->add_field( array(
			'name'       => __( 'Start Time', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. 09:00 AM', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_time_start',
			'type'       => 'text_time',
		) );

		$text_time_end = $item_date->add_field( array(
			'name'       => __( 'End Time', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. 10:30 AM', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_time_end',
			'type'       => 'text_time',
		) );

		if ( ! is_admin() || ! class_exists( '\Cmb2Grid\Grid\Cmb2Grid' ) ) {
			return;
		}
		$grid_layout = new \Cmb2Grid\Grid\Cmb2Grid( $item_date );
		$row_1 = $grid_layout->addRow();
		$row_1->addColumns( array( $title, $text_date, $text_time_start, $text_time_end ) );

	}/* cmb2_init__date() */


	public function cmb2_init__assignment_date() {

		$prefix = 'ubc_assignment_item_date_';

		$item_date = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Date/Time', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'assignment' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
			'show_names'	=> false,
		) );

		$title = $item_date->add_field( array(
			'name' => __( '', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'date_title',
			'desc' => __( 'Adding a date and time will add this item to the calendar automatically.', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );

		$text_date = $item_date->add_field( array(
			'name'       => __( 'Opening Date', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. MM/DD/YYYY', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_date',
			'type'       => 'text_date',
		) );

		$hidden_timestamp = $item_date->add_field( array(
			'name'       => __( 'Hidden Timestamp', \UBC\Press::get_text_domain() ),
			'desc'       => __( '', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'hidden_timestamp',
			'type'       => 'hidden',
		) );

		$text_time_start = $item_date->add_field( array(
			'name'       => __( 'Start Time', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. 09:00 AM', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_time_start',
			'type'       => 'text_time',
		) );

		$hidden_title = $item_date->add_field( array(
			'name' => __( '', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'date_title_hidden',
			'desc' => __( '&nbsp;', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );

		$text_date_end = $item_date->add_field( array(
			'name'       => __( 'Closing Date', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. MM/DD/YYYY', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_date_closing',
			'type'       => 'text_date',
		) );

		$text_time_end = $item_date->add_field( array(
			'name'       => __( 'End Time', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'e.g. 10:30 AM', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'item_time_end',
			'type'       => 'text_time',
		) );

		if ( ! is_admin() || ! class_exists( '\Cmb2Grid\Grid\Cmb2Grid' ) ) {
			return;
		}
		$grid_layout = new \Cmb2Grid\Grid\Cmb2Grid( $item_date );
		$row_1 = $grid_layout->addRow();
		$row_1->addColumns( array( $title, $text_date, $text_time_start ) );
		$row_2 = $grid_layout->addRow();
		$row_2->addColumns( array( $hidden_title, $text_date_end, $text_time_end ) );

	}/* cmb2_init__assignment_date() */


	/**
	 * When we save a lecture/assignment, we have a 'ubc_item_date_item_date' field
	 * That shows a nice user readable date, but to sort by that is a nightmare. So we
	 * use a hidden field which stores a unix timestamp of the value saved. We then use
	 * that value to orderby
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The post ID being saved
	 * @param (object) $post - the WP_Post object being saved
	 * @return null
	 */

	public function save_post__save_hidden_timestamp( $post_id, $post ) {
		// If this is just a revision, bail
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['nonce_CMB2phpubc_item_date_metabox'] ) && ! isset( $_POST['nonce_CMB2phpubc_assignment_item_date_metabox'] ) ) {
			return;
		}

		$post_type = sanitize_text_field( $_POST['post_type'] );

		if ( ! $post_type ) {
			return;
		}

		// Check nonces and required fields
		if ( 'assignment' === $post_type ) {

			if ( ! isset( $_POST['nonce_CMB2phpubc_assignment_item_date_metabox'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_assignment_item_date_metabox'], 'nonce_CMB2phpubc_assignment_item_date_metabox' ) ) {
				return;
			}

			if ( ! isset( $_POST['ubc_assignment_item_date_item_date_closing'] ) ) {
				return;
			}
		} else {

			if ( ! isset( $_POST['nonce_CMB2phpubc_item_date_metabox'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_item_date_metabox'], 'nonce_CMB2phpubc_item_date_metabox' ) ) {
				return;
			}

			if ( ! isset( $_POST['ubc_item_date_item_date'] ) ) {
				return;
			}
		}

		// Convert the format to a timestamp and sanitize it
		$date = ( 'assignment' !== $post_type ) ? \UBC\Press\Utils::sanitize_date( $_POST['ubc_item_date_item_date'] ) : \UBC\Press\Utils::sanitize_date( $_POST['ubc_assignment_item_date_item_date_closing'] );

		if ( empty( $date ) ) {
			return;
		}

		// Convert that date into a timestamp
		$timestamp = \DateTime::createFromFormat( 'm/d/Y', $date )->format( 'U' );

		// When publishing the post, we have to modify the $_POST directly otherwise it gets blanked
		$_POST['ubc_item_date_hidden_timestamp'] = $timestamp;

		// can't rely on $post_id apparently
		$saved_post_id = absint( $_POST['post_ID'] );
		$updated = update_post_meta( $saved_post_id, 'ubc_item_date_hidden_timestamp', $timestamp );

	}/* save_post__save_hidden_timestamp() */


	/**
	 * On the front-end we allow users to create notes about the page/post
	 * that they are currently viewing.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__user_notes() {

		$prefix = 'ubc_user_notes_';

		$user_notes = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Your notes', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'any' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
			'save_fields'	=> false,
		) );

		$notes = $user_notes->add_field( array(
			'name' => __( '', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'content',
			'desc' => __( '', \UBC\Press::get_text_domain() ),
			'type' => 'wysiwyg',
			'options' => array(
				'media_buttons' => false,
				'teeny' => true,
				'quicktags' => false,
				'editor_height' => 170,
			),
			// 'default' => array( $this, 'user_notes_default_content' ),
			'escape_cb' => array( $this, 'user_notes_default_content' ),
		) );

		// Hidden field for the AJAX endpoint URL
		$ajax_url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'user_notes', true, null, false );

		$ajax_url = $user_notes->add_field( array(
			'id' => 'user_notes_ajax_url',
			'type' => 'hidden',
			'default' => $ajax_url,
		) );

	}/* cmb2_init__user_notes() */


	/**
	 * Save the user notes.
	 *
	 * Saved to user meta in key ubc_press_user_notes which is
	 * array( <site id> => <section id> => <note content> )
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_init__save_user_notes() {

		// Test for nonce_CMB2phpubc_user_notes_metabox and verify
		if ( ! isset( $_POST['nonce_CMB2phpubc_user_notes_metabox'] ) || ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_user_notes_metabox'], 'nonce_CMB2phpubc_user_notes_metabox' ) ) {
			return;
		}

		// Sanitize
		$user_id		= absint( get_current_user_id() );
		$section_id		= absint( $_POST['object_id'] );
		$note_content	= wp_kses_post( $_POST['ubc_user_notes_content'] );

		$added = \UBC\Press\Utils::add_user_notes_for_object( $user_id, $section_id, $note_content );

	}/* cmb2_init__save_user_notes() */


	/**
	 * AJAX handler for saving user notes
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $request_data - the $_REQUEST data
	 * @return null
	 */

	public function ubcpressajax_user_notes__process( $request_data ) {

		// The nonce is already checked for us, still need to sanitize data
		$post_id 		= absint( $request_data['post_id'] );
		$user_id 		= get_current_user_id();
		$notes_content	= wp_kses_post( $request_data['notes_content'] );

		$complete 		= \UBC\Press\Utils::add_user_notes_for_object( $user_id, $post_id, $notes_content );

		// If we're coming from an AJAX request, send JSON
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

			if ( false === (bool) $complete ) {
				wp_send_json_error( array( 'message' => $complete ) );
			}

			wp_send_json_success( array( 'completed' => $complete ) );

		} else {

			$redirect_to = ( isset( $request_data['redirect_to'] ) ) ? esc_url( $request_data['redirect_to'] ) : false;

			// Otherwise, something went wrong somewhere, but we should not show a whitescreen, so redirect back to the component
			if ( false !== $redirect_to ) {
				header( 'Location: ' . $redirect_to );
			} else {
				header( 'Location: ' . get_permalink( $post_id ) );
			}
		}

	}/* ubcpressajax_user_notes__process() */


	/**
	 * By default the content of the notes field is empty, however, if
	 * the currently signed in user has made notes for this post already,
	 * then we show them
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function user_notes_default_content( $field_array, $field_object ) {

		if ( ! is_user_logged_in() ) {
			return '';
		}

		$all_user_notes	= \UBC\Press\Utils::get_user_notes( get_current_user_id() );
		$site_id	= get_current_blog_id();
		$object 	= get_the_ID();

		if ( empty( $all_user_notes ) || ! is_array( $all_user_notes ) ) {
			return '';
		}

		if ( ! isset( $all_user_notes[ $site_id ] ) ) {
			return '';
		}

		if ( ! isset( $all_user_notes[ $site_id ][ $object ] ) ) {
			return '';
		}

		return wp_kses_post( $all_user_notes[ $site_id ][ $object ]['content'] );

	}/* user_notes_default_content */

	/**
	 * The form for our user onboarding
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_admin_init__onboarding() {

		$prefix = 'ubc_press_onboarding_';

		$onboarding = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Course Details', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'all' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
		) );

		$session = $onboarding->add_field( array(
			'name' => __( 'Session', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'session',
			'type' => 'select',
			'default'          => 'winter',
		    'options'          => array(
		        'winter' => __( 'Winter', \UBC\Press::get_text_domain() ),
		        'summer' => __( 'Summer', \UBC\Press::get_text_domain() ),
		        'other'   => __( 'Other', \UBC\Press::get_text_domain() ),
		    ),
		) );

		$year = $onboarding->add_field( array(
			'name' => __( 'Year', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'year',
			'type' => 'text_small',
			'attributes' => array(
				'placeholder' => date( 'Y' ),
			),
		) );

		$all_faculties = \UBC\Press\Utils::get_faculty_list();

		$faculty = $onboarding->add_field( array(
			'name' => __( 'Faculty', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'faculty',
			'type' => 'select',
			'default' => '',
		    'options' => $all_faculties,
		) );

		// Departments are stored by school/fac, so we'll need a list. We'll
		// then hide them all in JS and only show the relevant one when a fac
		// is selected
		$all_departments = \UBC\Press\Utils::get_department_list();

		if ( $all_departments && is_array( $all_departments ) ) {

			foreach ( $all_departments as $fac => $depts ) {

				$faculty_real_name = $all_faculties[ $fac ];

				$dept = $onboarding->add_field( array(
					'name' => __( $faculty_real_name . ' Departments', \UBC\Press::get_text_domain() ),
					'id'   => $prefix . $fac . '_department',
					'type' => 'select',
					'default' => '',
				    'options' => $depts,
					'row_classes' => 'ubc_press_dept_list',
				) );
			}
		}

		$course_num = $onboarding->add_field( array(
			'name' => __( 'Course Number', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'course_num',
			'type' => 'text_small',
			'attributes' => array(
				'placeholder' => '1234',
			),
		) );

		$section_num = $onboarding->add_field( array(
			'name' => __( 'Section Number', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'section_num',
			'type' => 'text_small',
			'attributes' => array(
				'placeholder' => '1234',
			),
		) );

		// The main site in this network will have a post type 'programs'. This course
		// must be part of a program (so we're able to get the program objectives). So
		// we ask what program they're part of. This is a list of posts from the main site
		$available_programs = \UBC\Press\Utils::get_programs_for_current_site( true );

		if ( empty( $available_programs ) ) {
			return;
		}

		$program = $onboarding->add_field( array(
			'name' => __( 'Program', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'program',
			'type' => 'select',
			'default' => '',
		    'options' => $available_programs,
		) );

	}/* cmb2_admin_init__onboarding() */


	/**
	 * The meta box for the Program and Course objectives.
	 * Program objectives are set at the network level.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_admin_init__program_course_objectives() {

		$prefix = 'ubc_press_learning_objectives_';

		$objectives = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Learning Objectives', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'section' ),
			'context'    	=> 'side',
			'priority' 		=> 'low',
		) );

		// We need Network-level program objectives and site-specific course objectives
		// Course-specific is easy...
		$course_objectives = \UBC\Press\Utils::get_course_objectives( true );

		// Network-level
		$program_objectives = \UBC\Press\Utils::get_program_objectives( true );

		if ( ! empty( $course_objectives ) ) {
			$course_objectives = $objectives->add_field( array(
				'name'    => null,
				'id'      => $prefix . 'course_objectives',
				'type'    => 'multicheck',
				'select_all_button' => false,
				'options' => $course_objectives,
			) );
		}

		if ( ! empty( $program_objectives ) ) {
			$program_objectives = $objectives->add_field( array(
				'name'    => null,
				'id'      => $prefix . 'program_objectives',
				'type'    => 'multicheck',
				'select_all_button' => false,
				'options' => $program_objectives,
			) );
		}

	}/* cmb2_admin_init__program_course_objectives() */


	/**
	 * Save the onboarding form when it's filled in
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_admin_init__save_onboarding_options() {

		if ( ! isset( $_POST['nonce_CMB2phpubc_press_onboarding_metabox'] ) || ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_press_onboarding_metabox'], 'nonce_CMB2phpubc_press_onboarding_metabox' ) ) {
			return;
		}

		/*
		 * Departments are a little tricky. Each faculty has their own list
		 * of departments. In order to get the dept. list field, we look at
		 * the faculty saved value and use that to get the relevant dept. field
		 */
		$faculty 		= sanitize_text_field( $_POST['ubc_press_onboarding_faculty'] );

		$dept_field 	= ( isset( $_POST[ 'ubc_press_onboarding_' . $faculty . '_department' ] ) ) ? $_POST[ 'ubc_press_onboarding_' . $faculty . '_department' ] : false;

		$department 	= ( $dept_field ) ? sanitize_text_field( $dept_field ) : false;

		$session 		= sanitize_text_field( $_POST['ubc_press_onboarding_session'] );
		$year 			= absint( $_POST['ubc_press_onboarding_year'] );
		$course_num 	= sanitize_text_field( $_POST['ubc_press_onboarding_course_num'] );
		$section_num	= sanitize_text_field( $_POST['ubc_press_onboarding_section_num'] );
		$program		= sanitize_text_field( $_POST['ubc_press_onboarding_program'] );

		$option_name 	= 'ubc_press_course_details';

		$data_to_save = array(
			'session' 		=> $session,
			'course_dept' 	=> $department,
			'course_num' 	=> $course_num,
			'section_num'	=> $section_num,
			'year' 			=> $year,
			'course_fac' 	=> $faculty,
			'program'		=> $program,
		);

		update_option( $option_name, $data_to_save );

		// Test if we have all of the required details. If so, we also declare that we're done with onboarding
		$have_all_details = \UBC\Press\Onboarding\Utils::have_all_course_details( $data_to_save );

		if ( ! $have_all_details ) {
			return;
		}

		update_option( 'ubc_press_onboarded', date( 'U' ) );

		// Also flush rewrite rules on save to ensure this site is set up correctly (the plugin doesn't get "activated" as it's MU)
		flush_rewrite_rules();

	}/* cmb2_admin_init__save_onboarding_options() */


	/**
	 * A metabox which gives instructors the ability to set an icon
	 * for their subsection. We try and be clever and automatically
	 * select an appropriate icon based on the components added, but
	 * this gives the instructor the ability to override that.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_meta_boxes__subsection_icon() {

		$screens = array( 'section' );

		add_meta_box(
			'ubcpress_section_icon',
			__( 'Display Icon', 'ubc-press' ),
			array( $this, 'add_meta_box__icon_picker_markup' ),
			$screens,
			'side',
			'low'
		);

	}/* add_meta_boxes__subsection_icon() */

	public function save_post__save_icon_metabox( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['ubc_press_section_icon_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['ubc_press_section_icon_nonce'], 'ubc_press_section_icon' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['ubc_press_section_icon_picker'] ) ) {
			return;
		}

		// Sanitize user input.
		$saved_icon = sanitize_text_field( $_POST['ubc_press_section_icon_picker'] );

		// If the field is empty, we take a look at the components added to this subsection
		// Logic for auto-deciding on icon if empty
		if ( empty( $saved_icon ) ) {
			$saved_icon = static::determine_default_icon( $_POST['panels_data'] );
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, '_section_icon', $saved_icon );

	}/* save_post__save_icon_metabox() */


	/**
	 * Based on the provided panels data, determine what a good automatic icon
	 * for this section would be. The default is returned if there's no components
	 * or we're unsure what to do.
	 *
	 * If there's a quiz anywhere, we use that icon.
	 *
	 * If there's only one component, we use the icon for that component.
	 *
	 * If there's multiple components, and one has more instances than the others,
	 * we use the icon for that component.
	 *
	 * Otherwise we use the icon for the first component in the list.
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $content - The $_POST-d panels data
	 * @return (string) The string of the suggested icon
	 */

	public static function determine_default_icon( $content ) {

		// If our logic fails, this is the default
		$default = 'dashicons-portfolio';

		// Panels is a slashed, json string
		$panels_data = json_decode( wp_unslash( $content ), true );

		if ( empty( $panels_data ) || ! isset( $panels_data['widgets'] ) || empty( $panels_data['widgets'] ) ) {
			return $default;
		}

		$has_quiz = static::does_section_contain_a_quiz( $panels_data );

		// Found a quiz? We'll use that
		if ( true === $has_quiz ) {
			return \UBC\Press\Utils::get_component_icon( 'AddQuizWidget' );
		}

		// Only one component? Use icon for that
		if ( 1 === count( $panels_data['widgets'] ) ) {

			$widget_type = \UBC\Press\Utils::get_panels_widget_type( $panels_data['widgets'][0]['panels_info']['class'] );
			return \UBC\Press\Utils::get_component_icon( $widget_type );
		}

		$widget_types = array();

		foreach ( $panels_data['widgets'] as $wid => $wwidget ) {
			$widget_class = $wwidget['panels_info']['class'];
			if ( array_key_exists( $widget_class, $widget_types ) ) {
				$widget_types[ $widget_class ] = $widget_types[ $widget_class ] + 1;
			} else {
				$widget_types[ $widget_class ] = 1;
			}
		}

		// Sort the array so the most popular is on top
		arsort( $widget_types );
		// reset() allows us to then get the first item
		reset( $widget_types );
		// key gives us the key of the first (hence most popular) item
		$pop_widget_type = key( $widget_types );

		$widget_type = \UBC\Press\Utils::get_panels_widget_type( $pop_widget_type );
		return \UBC\Press\Utils::get_component_icon( $widget_type );

	}/* determine_default_icon() */

	/**
	 * Helper method to determine if a section contains a quiz component
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $panels_data - The stored panels_data meta for a post
	 * @return (bool) true if the section contains a quiz.
	 */

	public static function does_section_contain_a_quiz( $panels_data ) {

		if ( ! is_array( $panels_data ) || ! isset( $panels_data['widgets'] ) || empty( $panels_data['widgets'] ) ) {
			return false;
		}

		$widgets = $panels_data['widgets'];

		foreach ( $widgets as $id => $widget ) {
			if ( array_key_exists( 'quiz_post_id', $widget ) ) {
				return true;
			}
		}

		return false;

	}/* does_section_contain_a_quiz() */


	public function add_meta_box__icon_picker_markup( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'ubc_press_section_icon', 'ubc_press_section_icon_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_section_icon', true );

		_e( '<input class="" value="' . esc_attr( $value ) . '" id="ubc_press_section_icon_picker" name="ubc_press_section_icon_picker" type="text" />' );
		_e( '<input class="button dashicons-picker" type="button" value="Choose Icon" data-target="#ubc_press_section_icon_picker" />' );

	}


	/**
	 * Display the markup for when a form hasn't been associated with an assignment
	 * yet
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function cmb2_admin_init__show_assignment_form_create_form_markup( $post ) {

		// Test if we have an associated form ID
		$post_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : false;
		$post_id = absint( $post_id );

		if ( ! $post_id  ) {
			$this->show_create_assignment_form_markup();
			return;
		}

		$associated_gform = get_post_meta( $post_id, 'associated_form_id', true );
		if ( false === $associated_gform || empty( $associated_gform ) ) {
			$this->show_create_assignment_form_markup();
			return;
		}

		$this->show_assignment_form_attached_markup( $post_id );

	}/* cmb2_admin_init__show_assignment_form_create_form_markup() */


	/**
	 * Display the markup for creating an assignment form
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function show_create_assignment_form_markup() {

		$prefix = 'ubc_press_create_assignment_form_';

		$create_assignment_form = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Create Assignment Form', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'Assignment' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
		) );

		$create_assignment_form->add_field( array(
			'name' => __( 'What\'s this?', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'title',
			'desc' => __( 'Each assignment component contains a customizable form. Fill out some details below and click the "Create Assignment Form" button. A form will be made and associated with this component. Any submissions of that form will be associated with this form. We will automatically take the title of this assignment and use that for the form and also set the opening and closing dates for submission based on the details you provide.', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );

		$with_textarea = $create_assignment_form->add_field( array(
			'name'	=> __( 'Content Submission Type', \UBC\Press::get_text_domain() ),
			'desc' 	=> __( 'How would you like your students to submit their content? As a file upload (.pdf, .doc, .docx, .pages) or enter it as text in a WYSIWYG editor, or both?', \UBC\Press::get_text_domain() ),
			'id'  	=> $prefix . 'text_area_or_file_upload_or_both',
			'type'	=> 'radio',
			'options' => array(
				'file_upload'	=> __( 'File Upload', \UBC\Press::get_text_domain() ),
				'textarea' 		=> __( 'WYSIWYG', \UBC\Press::get_text_domain() ),
				'both'			=> __( 'Both', \UBC\Press::get_text_domain() ),
			),
			'after_row'	=> array( $this, 'after__submit_button_for_assignment_form' ),
		) );

	}/* show_create_assignment_form_markup() */


	/**
	 * The 'Create Assignment Form' metabox needs to be able to allow for separate
	 * submission. This callback adds a button.
	 *
	 * @since 1.0.0
	 *
	 * @param  (array) $field_args Array of field parameters
	 * @param  (CMB2_Field object) $field Field object
	 * @return null
	 */

	public function after__submit_button_for_assignment_form( $field_args, $field ) {

		// Need the AJAX URL
		$ajax_url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'create_assignment_form', true, false );

		echo '<p><input type="submit" data-ajax_url="' . $ajax_url . '" name="create_assignment_form" id="create_assignment_form" class="button button-primary button-large" value="Create Assignment Form"></p>';

	}/* after__submit_button_for_assignment_form() */


	/**
	 * Display the markup for the details of the attached gravity form
	 * for this assignment
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID assignment post we're currently editing
	 * @return null
	 */

	public function show_assignment_form_attached_markup( $post_id ) {

		$prefix = 'ubc_press_attached_assignment_form_';

		$associated_form_id = get_post_meta( absint( $post_id ), 'associated_form_id', true );

		$create_assignment_form = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Assignment Form Details', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'Assignment' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
		) );

		$metabox_content = $this->get_content_for_already_assigned_form( $associated_form_id );

		$create_assignment_form->add_field( array(
			'name' => __( 'What\'s this?', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'title',
			'desc' => $metabox_content,
			'type' => 'title',
		) );

	}/* show_assignment_form_attached_markup() */



	/**
	 * Returns the content displayed to the user when the assignment they
	 * are viewing is already associated with a form.
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $form_id - The associated form
	 * @return (string) The content to show for this associated form
	 */


	public function get_content_for_already_assigned_form( $form_id ) {

		// Build the gravity form edit form link: wp-admin/admin.php?page=gf_edit_forms&id=3
		$form_edit_url = $this->get_form_edit_url( $form_id );

		$content = __( 'You have associated a <a href="' . $form_edit_url . '" title="">form</a> with this assignment. You may <a href="' . $form_edit_url . '" title="">edit the form</a> using the form builder.', \UBC\Press::get_text_domain() );

		return $content;

	}/* get_content_for_already_assigned_form() */


	/**
	 * Returns the admin edit url for the passed form ID
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $form_id - The ID of the form
	 * @return (string) The URL of the edit screen for the passed form ID
	 */

	public function get_form_edit_url( $form_id ) {

		$form_id = absint( $form_id );

		if ( ! $form_id ) {
			return false;
		}

		$url = admin_url( 'admin.php?page=gf_edit_forms&id=' . $form_id );

		return esc_url( $url );

	}/* get_form_edit_url() */

	/**
	 * Creation of a Gravity Form for assignment submissions. When a new assignment
	 * is created, the user can create and associated a gravity form. The form is
	 * created here using the gForm API and then meta associates the two.
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $request_data - the $_REQUEST data
	 * @return null
	 */

	public function ubcpressajax_create_assignment_form__process( $request_data ) {

		// The nonce is already checked for us, still need to sanitize data
		$submission_type	= sanitize_text_field( $request_data['submissionType'] ); // file_upload/textarea/both
		$title 				= sanitize_title( $request_data['titleField'] );
		$date 				= \UBC\Press\Utils::sanitize_date( $request_data['dateField'] );
		$date_end 			= \UBC\Press\Utils::sanitize_date( $request_data['dateFieldEnd'] );
		$start_time 		= \UBC\Press\Utils::sanitize_time( $request_data['startTimeField'] );
		$end_time 			= \UBC\Press\Utils::sanitize_time( $request_data['endTimeField'] );
		$post_id			= absint( $request_data['postID'] );

		// We need Gravity Forms
		if ( ! class_exists( 'RGFormsModel' ) ) {
			wp_send_json_error( array( 'message' => __( 'Gravity Forms is not active', \UBC\Press::get_text_domain() ) ) );
			return;
		}

		// Check if a form with this title already exists
		if ( \UBC\Press\Utils::gform_exists( $title ) ) {
			wp_send_json_error( array( 'message' => __( 'A form with this name already exists', \UBC\Press::get_text_domain() ) ) );
			return;
		}

		// We need to split the start and end times
		$start_time_parts	= $this->TARDIS( $start_time );
		$end_time_parts		= $this->TARDIS( $end_time );

		// Generate a sign in link
		$sign_in_link = wp_kses_post( '<a href="' . wp_login_url( get_permalink() ) . '" title="">' . __( 'Sign In', \UBC\Press::get_text_domain() ) . '</a>' );

		// OK form doesn't exist, let's make one
		$form_array = array();

		// Form properties
		$form_array['title'] = $title;
		$form_array['date_created'] = date( 'Y-m-d H:i:s' );
		$form_array['useCurrentUserAsAuthor'] = true;
		$form_array['postTitleTemplate'] = '{Your Name:1} - {form_title}';
		$form_array['postStatus'] = 'pending';
		$form_array['requireLogin'] = true;
		$form_array['is_active'] = true;
		$form_array['cssClass'] = 'ubc-press-assignment-form';

		// Scheduling
		$form_array['scheduleForm'] = true;
		$form_array['scheduleStart'] = $date;
		$form_array['scheduleEnd'] = $date_end;

		$form_array['scheduleStartHour'] = $start_time_parts['hour'];
		$form_array['scheduleStartMinute'] = $start_time_parts['minute'];
		$form_array['scheduleStartAmpm'] = $start_time_parts['AMPM'];
		$form_array['scheduleEndHour'] = $end_time_parts['hour'];
		$form_array['scheduleEndMinute'] = $end_time_parts['minute'];
		$form_array['scheduleEndAmpm'] = $end_time_parts['AMPM'];

		$form_array['schedulePendingMessage'] = __( 'This assignment can be submitted after ' . $start_time . ' on ' .  $date . '.', \UBC\Press::get_text_domain() );
		$form_array['scheduleMessage'] = __( 'This assignment can no longer be submitted.', \UBC\Press::get_text_domain() );

		// Require login
		$form_array['requireLogin'] = true;
		$form_array['requireLoginMessage'] = __( 'You must ' . $sign_in_link .' to submit this assignment', \UBC\Press::get_text_domain() );

		// Confirmations
		$conf_id = \UBC\Press\Utils::random_string_of_length( 13 );
		$form_array['confirmations'] = array(
			$conf_id => array(
				'id' 		=> $conf_id,
				'name' 		=> 'Default Confirmation',
				'isDefault'	=> 1,
				'type' 		=> 'message',
				'message' 	=> 'Your assignment has been received. Here is a copy of what you submitted:

{all_fields}',
			),
		);

		// Add the default fields: Name, Email, Entry ID, Assignment ID
		$form_array['fields'] = array(
			array(
				'id' => 1,
				'label' => 'Your Name',
				'type' => 'post_title',
				'isRequired' => true,
				'size' => 'medium',
				'defaultValue' => '{user:display_name}',

			),
			array(
				'id' => 2,
				'label' => 'Your Email Address',
				'inputType' => 'email',
				'defaultValue' => '{user:user_email}',
				'postCustomFieldName' => '_user_email_address',
				'adminLabel' => '',
				'type' => 'post_custom_field',
				'isRequired' => true,
			),
			array(
				'id' => '1000',
				'type' => 'hidden',
				'label' => 'Associated Assignment ID',
				'defaultValue' => esc_html( $post_id ),
			),
		);

		$file_upload_args = array(
			'id' => 3,
			'label' => 'Assignment File',
			'inputType' => 'fileupload',
			'postCustomFieldName' => '_assignment_file',
			'adminLabel' => '',
			'allowedExtensions' => 'pdf, doc, docx, txt',
			'type' => 'post_custom_field',
			'isRequired' => true,
		);

		$textarea_args = array(
			'id' => 4,
			'label' => 'Assignment Text',
			'adminLabel' => '',
			'type' => 'post_content',
			'size' => 'medium',
			'isRequired' => true,
		);

		// Now conditionally add the fields selected via the form input
		switch ( $submission_type ) {

			case 'file_upload':

				$form_array['fields'][] = $file_upload_args;
				break;

			case 'textarea':

				$form_array['fields'][] = $textarea_args;
				break;

			case 'both':
			default:

				$form_array['fields'][] = $file_upload_args;
				$form_array['fields'][] = $textarea_args;
				break;

		}

		$result = \GFAPI::add_form( $form_array );

		// If we're coming from an AJAX request, send JSON
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

			if ( false === (bool) $result ) {
				wp_send_json_error( array( 'message' => $result ) );
			}

			// OK, now associated that form with this post
			add_post_meta( $post_id, 'associated_form_id', $result );

			wp_send_json_success( array(
				'completed' => true,
				'associated_form_id' => $result,
				'metabox_content' => $this->get_content_for_already_assigned_form( $result ),
			) );

		} else {

			$redirect_to = ( isset( $request_data['redirect_to'] ) ) ? esc_url( $request_data['redirect_to'] ) : false;

			// Otherwise, something went wrong somewhere, but we should not show a whitescreen, so redirect back
			if ( false !== $redirect_to ) {
				header( 'Location: ' . $redirect_to );
			} else {
				header( 'Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] );
			}
		}

	}/* ubcpressajax_create_assignment_form__process() */



	/**
	 * When a Gravityform is submitted we check if this is an assignment. If it is
	 * we make a 'submission' post. We then associate that submission with the
	 * assignment component post which holds the form.
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $entry - The entry that was just created.
	 * @param (array) $form - The current form.
	 * @return null
	 */

	public function gform_after_submission__create_submission_for_assignments( $entry, $form ) {

		// The FormID is stored as post meta under 'associated_form_id'
		$form_id = $form['id'];

		$associated_assignment_post_id = static::get_post_id_of_assignment_form( $form_id );

		// Bail if this isn't for an assignment
		if ( false === $associated_assignment_post_id ) {
			return;
		}

		$new_submission_post_id = $this->make_submission_post( $entry, $form );

		$this->associate_submission_with_assignment( $new_submission_post_id, $associated_assignment_post_id );

	}/* gform_after_submission__create_submission_for_assignments() */



	/**
	 * When a gForm has a post title field, it auto creates a post. That's annoying.
	 * Let's stop that for assignments as we create our own.
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) $is_disabled - Is post creation disabled for this form?
	 * @param (array) $form - The gravityform properties
	 * @param (array) $entry - The gravityform entry
	 * @return (bool) Whether this form should have disabled post creation
	 */

	public function gform_disable_post_creation__stop_auto_post_creation_for_assignments( $is_disabled, $form, $entry ) {

		// The FormID is stored as post meta under 'associated_form_id'
		$form_id = $form['id'];

		$associated_assignment_post_id = static::get_post_id_of_assignment_form( $form_id );

		// Bail if this isn't for an assignment
		if ( false === $associated_assignment_post_id ) {
			return $is_disabled;
		}

		return true;

	}/* gform_disable_post_creation__stop_auto_post_creation_for_assignments() */

	/**
	 * Return the Post ID of the assignment component whose 'associated_form_id'
	 * matches the passed $id
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $id - The meta_value we're looking for within associated_form_id
	 * @return (int|false) $assignment_post_id The Post ID of the assignment component
	 */


	public static function get_post_id_of_assignment_form( $id ) {

		$results = new \WP_Query( array(
			'post_type' => 'assignment',
			'meta_key' => 'associated_form_id',
			'meta_value' => $id,
		) );

		$post_count = ( isset( $results->post_count ) ) ? $results->post_count : false;

		if ( false === $post_count || 0 === $post_count ) {
			return false;
		}

		$post = $results->posts[0];

		$post_id = $post->ID;

		return $post_id;

	}/* get_post_id_of_assignment_form() */



	/**
	 * Make a submission post based on the submitted gForm entry
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $entry - The GForm entry that has been created
	 * @param (array) $form - The GForm submitted
	 * @return (int) $post_id - The ID of the newly created submission post
	 */

	public function make_submission_post( $entry, $form ) {
		$title_field = $this->get_title_field_id_from_form( $form );
		$new_post_args = array(
			'post_author' => get_current_user_id(),
			'post_title' => $entry[ $title_field ] . ' - ' . $form['title'],
			'post_type' => 'submission',
		);
		$new_post_id = wp_insert_post( $new_post_args, true );
		return $new_post_id;

	}/* make_submission_post() */



	/**
	 * Get the title field ID for a submitted form
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $form - A gForm form
	 * @return (int) The field ID for an entry for the title field
	 */


	public function get_title_field_id_from_form( $form ) {
		$title_field_id = false;

		foreach ( $form['fields'] as $key => $field_object ) {

			if ( is_a( $field_object, 'GF_Field_Post_Title' ) ) {
				$title_field_id = $field_object->id;
			}
		}

		return $title_field_id;

	}/* get_title_field_id_from_form() */



	/**
	 * Associate a newly made submission post with the assignment form
	 * component that created it
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $submission_id - The newly made submission post ID
	 * @param (int) $assignment_id - The assignment component ID
	 * @return null
	 */

	public function associate_submission_with_assignment( $submission_id, $assignment_id ) {

		add_post_meta( $submission_id, 'associated_assignment_id', $assignment_id );

		// Also, for the assignment, add it to an array of submissions
		$existing_submissions = get_post_meta( $assignment_id, 'associated_submissions', true );
		if ( empty( $existing_submissions ) || ! is_array( $existing_submissions ) ) {
			$existing_submissions = array();
		}

		$existing_submissions[] = $submission_id;

		update_post_meta( $assignment_id, 'associated_submissions', $existing_submissions );

	}/* associate_submission_with_assignment() */


	/**
	 * Gravity Forms requires times to be in distinct chunks. We receive a time
	 * such as 10:00 AM and gForms needs the Hour, Minute and AM/PM as separate
	 * fields. This method provides a way to do that and returns an array of the
	 * parts.
	 *
	 * Why is this method called TARDIS? Becuase we explode Time and Space.
	 * props @thoronas for the name.
	 *
	 * @TODO: Make this a bit smarter for if we have a 24 hour clock
	 *
	 * @since 0.5.0
	 *
	 * @param ($string) $time - Time as a string, i.e. 10:00 AM
	 * @return (array) The distinct parts as an associative array
	 */

	public function TARDIS( $time ) {

		// Sanitize
		$time		= \UBC\Press\Utils::sanitize_time( $time );

		// Bail early if we don't have the format we expect
		if ( empty( $time ) ) {
			return array();
		}

		// First; get the am/pm by splitting on a space
		// We're, err, exploding time and space. Geddit? Don't judge.
		$ampm_parts	= explode( ' ', $time );
		$ampm		= $ampm_parts[1]; // Will be "AM" or "PM"

		$just_time	= $ampm_parts[0]; // Will just be i.e. 10:00

		// Split on the colon to get the hour and minute
		$time_parts	= explode( ':', $just_time );
		$hour		= $time_parts[0];
		$minute		= $time_parts[1];

		$time_parts = array(
			'AMPM'	=> $ampm,
			'hour'	=> $hour,
			'minute' => $minute,
		);

		/**
		 * Filters An array of time parts made by converting a time from a
		 * HH:MM (A/P)M format.
		 *
		 * @since 0.5.0
		 *
		 * @param (array) $time_parts - An associative array containing the hour and minute and (A/P)M
		 * @param (string) $time The unconverted time string
		 */

		return apply_filters( 'ubc_press_time_parts', $time_parts, $time );

	}/* TARDIS() */

	function cmb2_init__test() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_yourprefix_demo_';
		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$cmb_demo = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Test Metabox', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'assignment' ), // Post type
			// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
			// 'context'    => 'normal',
			// 'priority'   => 'high',
			// 'show_names' => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$cmb_demo->add_field( array(
			'name'       => __( 'Test Text', \UBC\Press::get_text_domain() ),
			'desc'       => __( 'field description (optional)', \UBC\Press::get_text_domain() ),
			'id'         => $prefix . 'text',
			'type'       => 'text',
			'show_on_cb' => 'yourprefix_hide_if_no_cats', // function should return a bool value
			// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
			// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
			// 'on_front'        => false, // Optionally designate a field to wp-admin only
			// 'repeatable'      => true,
		) );

		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Small', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textsmall',
			'type' => 'text_small',
			// 'repeatable' => true,
		) );

		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Medium', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textmedium',
			'type' => 'text_medium',
			// 'repeatable' => true,
		) );
		$test_field_1 = $cmb_demo->add_field( array(
			'name' => __( 'Website URL', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'url',
			'type' => 'text_url',
			// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
			// 'repeatable' => true,
		) );

		$test_field_2 = $cmb_demo->add_field( array(
			'name' => __( 'Test Text Email', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'email',
			'type' => 'text_email',
			// 'repeatable' => true,
		) );
		$test_field_3 = $cmb_demo->add_field( array(
			'name' => __( 'Test Time', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'time',
			'type' => 'text_time',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Time zone', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'timezone',
			'type' => 'select_timezone',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date Picker', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textdate',
			'type' => 'text_date',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date Picker (UNIX timestamp)', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textdate_timestamp',
			'type' => 'text_date_timestamp',
			// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date/Time Picker Combo (UNIX timestamp)', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'datetime_timestamp',
			'type' => 'text_datetime_timestamp',
		) );
		// This text_datetime_timestamp_timezone field type
		// is only compatible with PHP versions 5.3 or above.
		// Feel free to uncomment and use if your server meets the requirement
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'datetime_timestamp_timezone',
			'type' => 'text_datetime_timestamp_timezone',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Money', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textmoney',
			'type' => 'text_money',
			// 'before_field' => '£', // override '$' symbol if needed
			// 'repeatable' => true,
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Color Picker', \UBC\Press::get_text_domain() ),
			'id'      => $prefix . 'colorpicker',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textarea',
			'type' => 'textarea',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area Small', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textareasmall',
			'type' => 'textarea_small',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area for Code', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'textarea_code',
			'type' => 'textarea_code',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Title Weeeee', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'title',
			'desc' => __( 'Titles can have descriptions, too', \UBC\Press::get_text_domain() ),
			'type' => 'title',
		) );
		$cmb_demo->add_field( array(
			'name'             => __( 'Test Select', \UBC\Press::get_text_domain() ),
			'id'               => $prefix . 'select',
			'type'             => 'select',
			'show_option_none' => true,
			'options'          => array(
				'standard' => __( 'Option One', \UBC\Press::get_text_domain() ),
				'custom'   => __( 'Option Two', \UBC\Press::get_text_domain() ),
				'none'     => __( 'Option Three', \UBC\Press::get_text_domain() ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'             => __( 'Test Radio inline', \UBC\Press::get_text_domain() ),
			'id'               => $prefix . 'radio_inline',
			'type'             => 'radio_inline',
			'show_option_none' => 'No Selection',
			'options'          => array(
				'standard' => __( 'Option One', \UBC\Press::get_text_domain() ),
				'custom'   => __( 'Option Two', \UBC\Press::get_text_domain() ),
				'none'     => __( 'Option Three', \UBC\Press::get_text_domain() ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Radio', \UBC\Press::get_text_domain() ),
			'id'      => $prefix . 'radio',
			'type'    => 'radio',
			'options' => array(
				'option1' => __( 'Option One', \UBC\Press::get_text_domain() ),
				'option2' => __( 'Option Two', \UBC\Press::get_text_domain() ),
				'option3' => __( 'Option Three', \UBC\Press::get_text_domain() ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Radio', \UBC\Press::get_text_domain() ),
			'id'       => $prefix . 'text_taxonomy_radio',
			'type'     => 'taxonomy_radio',
			'taxonomy' => 'category', // Taxonomy Slug
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Select', \UBC\Press::get_text_domain() ),
			'id'       => $prefix . 'taxonomy_select',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'category', // Taxonomy Slug
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Multi Checkbox', \UBC\Press::get_text_domain() ),
			'id'       => $prefix . 'multitaxonomy',
			'type'     => 'taxonomy_multicheck',
			'taxonomy' => 'post_tag', // Taxonomy Slug
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Checkbox', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'checkbox',
			'type' => 'checkbox',
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Multi Checkbox', \UBC\Press::get_text_domain() ),
			'id'      => $prefix . 'multicheckbox',
			'type'    => 'multicheck',
			// 'multiple' => true, // Store values in individual rows
			'options' => array(
				'check1' => __( 'Check One', \UBC\Press::get_text_domain() ),
				'check2' => __( 'Check Two', \UBC\Press::get_text_domain() ),
				'check3' => __( 'Check Three', \UBC\Press::get_text_domain() ),
			),
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test wysiwyg', \UBC\Press::get_text_domain() ),
			'id'      => $prefix . 'wysiwyg',
			'type'    => 'wysiwyg',
			'options' => array( 'textarea_rows' => 5 ),
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Image', \UBC\Press::get_text_domain() ),
			'desc' => __( 'Upload an image or enter a URL.', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'image',
			'type' => 'file',
		) );
		$cmb_demo->add_field( array(
			'name'         => __( 'Multiple Files', \UBC\Press::get_text_domain() ),
			'desc'         => __( 'Upload or add multiple images/attachments.', \UBC\Press::get_text_domain() ),
			'id'           => $prefix . 'file_list',
			'type'         => 'file_list',
			'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'oEmbed', \UBC\Press::get_text_domain() ),
			'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'embed',
			'type' => 'oembed',
		) );
		$cmb_demo->add_field( array(
			'name'         => 'Testing Field Parameters',
			'id'           => $prefix . 'parameters',
			'type'         => 'text',
			'before_row'   => 'yourprefix_before_row_if_2', // callback
			'before'       => '<p>Testing <b>"before"</b> parameter</p>',
			'before_field' => '<p>Testing <b>"before_field"</b> parameter</p>',
			'after_field'  => '<p>Testing <b>"after_field"</b> parameter</p>',
			'after'        => '<p>Testing <b>"after"</b> parameter</p>',
			'after_row'    => '<p>Testing <b>"after_row"</b> parameter</p>',
		) );

		if ( ! is_admin() ) {
			return;
		}
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_demo );
		$row_1 = $cmb2Grid->addRow();
		$row_1->addColumns( array( $test_field_1, $test_field_2, $test_field_3 ) );

	}

}/* class Setup */
