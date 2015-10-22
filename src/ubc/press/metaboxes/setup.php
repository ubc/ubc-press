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


class Setup {

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

		// Add a section description metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__section_description' ) );

		// Add a handout details metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__handout_details' ) );

		// Add a URL link to the links post type
		add_action( 'cmb2_init', array( $this, 'cmb2_init__link_details' ) );

		// Course settings metabox
		add_action( 'cmb2_init', array( $this, 'cmb2_init__course_settings' ) );

		// Add a date metabox to most post types
		add_action( 'cmb2_init', array( $this, 'cmb2_init__date' ) );

		// This is the front-end notes submission form
		add_action( 'cmb2_init', array( $this, 'cmbs_init__user_notes' ) );

		// add_action( 'cmb2_init', array( $this, 'cmb2_init__test' ) );
		// When a post is saved that contains the date/time, we need to save the date as a hidden timestamp
		add_action( 'save_post', array( $this, 'save_post__save_hidden_timestamp' ), 100, 2 );
		add_action( 'publish_post', array( $this, 'save_post__save_hidden_timestamp' ), 100, 2 );

	}/* create() */


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
				'textarea_rows' => 5,
				'media_buttons' => false,
				'teeny' => true,
			),
		) );

		if ( ! is_admin() ) {
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
			'object_types'  => array( 'lecture', 'assignment' ),
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

		if ( ! is_admin() ) {
			return;
		}
		$grid_layout = new \Cmb2Grid\Grid\Cmb2Grid( $item_date );
		$row_1 = $grid_layout->addRow();
		$row_1->addColumns( array( $title, $text_date, $text_time_start, $text_time_end ) );

	}/* cmb2_init__date() */



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

		if ( ! isset( $_POST['nonce_CMB2phpubc_item_date_metabox'] ) ) {
			return;
		}

		// See if $_POST contains ubc_item_date_item_date
		if ( ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_item_date_metabox'], 'nonce_CMB2phpubc_item_date_metabox' ) || ! isset( $_POST['ubc_item_date_item_date'] ) ) {
			return;
		}

		// Convert the format to a timestamp and sanitize it
		$date = \UBC\Press\Utils::sanitize_date( $_POST['ubc_item_date_item_date'] );

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

	public function cmbs_init__user_notes() {

		$prefix = 'ubc_user_notes_';

		$user_notes = new_cmb2_box( array(
			'id'			=> $prefix . 'metabox',
			'title'			=> __( 'Your notes', \UBC\Press::get_text_domain() ),
			'object_types'  => array( 'any' ),
			'context'    	=> 'normal',
			'priority' 		=> 'low',
		) );

		$notes = $user_notes->add_field( array(
			'name' => __( '', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'content',
			'desc' => __( '', \UBC\Press::get_text_domain() ),
			'type' => 'textarea',
			'default' => array( $this, 'user_notes_default_content' ),
		) );

	}/* cmbs_init__user_notes() */


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

	public function user_notes_default_content() {

		return 'weeee';

	}/* user_notes_default_content */


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


	/**
	 * Run before we create any custom metaboxes. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_create_all_metaboxes' );

	}/* before() */



	/**
	 * Run an action after we create all metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_create_all_metaboxes' );

	}/* after() */

}/* class Setup */
