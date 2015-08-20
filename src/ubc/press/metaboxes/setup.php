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

		// add_action( 'cmb2_init', array( $this, 'cmb2_init__test' ) );

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

		$prefix = '_section_description';

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

		$prefix = '_handout_details';

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
			'desc'         => __( 'Upload or add multiple images/attachments.', 'cmb2' ),
			'id'           => $prefix . 'file_list',
			'type'         => 'file_list',
			'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		) );

		$handout_description = $handout_details->add_field( array(
			'name' => __( 'Handout Description', \UBC\Press::get_text_domain() ),
			'id'   => $prefix . 'description',
			'type' => 'textarea',
			'desc' => __( 'A brief description of the handout, perhaps the file type, size or contents of a zip file.', \UBC\Press::get_text_domain() ),
		) );

	}/* cmb2_init__handout_details() */


	function cmb2_init__test() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_yourprefix_demo_';
		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$cmb_demo = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Test Metabox', 'cmb2' ),
			'object_types'  => array( 'assignment' ), // Post type
			// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
			// 'context'    => 'normal',
			// 'priority'   => 'high',
			// 'show_names' => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );
		$cmb_demo->add_field( array(
			'name'       => __( 'Test Text', 'cmb2' ),
			'desc'       => __( 'field description (optional)', 'cmb2' ),
			'id'         => $prefix . 'text',
			'type'       => 'text',
			'show_on_cb' => 'yourprefix_hide_if_no_cats', // function should return a bool value
			// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
			// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
			// 'on_front'        => false, // Optionally designate a field to wp-admin only
			// 'repeatable'      => true,
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Small', 'cmb2' ),
			'id'   => $prefix . 'textsmall',
			'type' => 'text_small',
			// 'repeatable' => true,
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Medium', 'cmb2' ),
			'id'   => $prefix . 'textmedium',
			'type' => 'text_medium',
			// 'repeatable' => true,
		) );
		$test_field_1 = $cmb_demo->add_field( array(
			'name' => __( 'Website URL', 'cmb2' ),
			'id'   => $prefix . 'url',
			'type' => 'text_url',
			// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
			// 'repeatable' => true,
		) );

		$test_field_2 = $cmb_demo->add_field( array(
			'name' => __( 'Test Text Email', 'cmb2' ),
			'id'   => $prefix . 'email',
			'type' => 'text_email',
			// 'repeatable' => true,
		) );
		$test_field_3 = $cmb_demo->add_field( array(
			'name' => __( 'Test Time', 'cmb2' ),
			'id'   => $prefix . 'time',
			'type' => 'text_time',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Time zone', 'cmb2' ),
			'id'   => $prefix . 'timezone',
			'type' => 'select_timezone',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date Picker', 'cmb2' ),
			'id'   => $prefix . 'textdate',
			'type' => 'text_date',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date Picker (UNIX timestamp)', 'cmb2' ),
			'id'   => $prefix . 'textdate_timestamp',
			'type' => 'text_date_timestamp',
			// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date/Time Picker Combo (UNIX timestamp)', 'cmb2' ),
			'id'   => $prefix . 'datetime_timestamp',
			'type' => 'text_datetime_timestamp',
		) );
		// This text_datetime_timestamp_timezone field type
		// is only compatible with PHP versions 5.3 or above.
		// Feel free to uncomment and use if your server meets the requirement
		$cmb_demo->add_field( array(
			'name' => __( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', 'cmb2' ),
			'id'   => $prefix . 'datetime_timestamp_timezone',
			'type' => 'text_datetime_timestamp_timezone',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Money', 'cmb2' ),
			'id'   => $prefix . 'textmoney',
			'type' => 'text_money',
			// 'before_field' => '£', // override '$' symbol if needed
			// 'repeatable' => true,
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Color Picker', 'cmb2' ),
			'id'      => $prefix . 'colorpicker',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area', 'cmb2' ),
			'id'   => $prefix . 'textarea',
			'type' => 'textarea',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area Small', 'cmb2' ),
			'id'   => $prefix . 'textareasmall',
			'type' => 'textarea_small',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Text Area for Code', 'cmb2' ),
			'id'   => $prefix . 'textarea_code',
			'type' => 'textarea_code',
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Title Weeeee', 'cmb2' ),
			'id'   => $prefix . 'title',
			'desc' => __( 'Titles can have descriptions, too', 'cmb2' ),
			'type' => 'title',
		) );
		$cmb_demo->add_field( array(
			'name'             => __( 'Test Select', 'cmb2' ),
			'id'               => $prefix . 'select',
			'type'             => 'select',
			'show_option_none' => true,
			'options'          => array(
				'standard' => __( 'Option One', 'cmb2' ),
				'custom'   => __( 'Option Two', 'cmb2' ),
				'none'     => __( 'Option Three', 'cmb2' ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'             => __( 'Test Radio inline', 'cmb2' ),
			'id'               => $prefix . 'radio_inline',
			'type'             => 'radio_inline',
			'show_option_none' => 'No Selection',
			'options'          => array(
				'standard' => __( 'Option One', 'cmb2' ),
				'custom'   => __( 'Option Two', 'cmb2' ),
				'none'     => __( 'Option Three', 'cmb2' ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Radio', 'cmb2' ),
			'id'      => $prefix . 'radio',
			'type'    => 'radio',
			'options' => array(
				'option1' => __( 'Option One', 'cmb2' ),
				'option2' => __( 'Option Two', 'cmb2' ),
				'option3' => __( 'Option Three', 'cmb2' ),
			),
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Radio', 'cmb2' ),
			'id'       => $prefix . 'text_taxonomy_radio',
			'type'     => 'taxonomy_radio',
			'taxonomy' => 'category', // Taxonomy Slug
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Select', 'cmb2' ),
			'id'       => $prefix . 'taxonomy_select',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'category', // Taxonomy Slug
		) );
		$cmb_demo->add_field( array(
			'name'     => __( 'Test Taxonomy Multi Checkbox', 'cmb2' ),
			'id'       => $prefix . 'multitaxonomy',
			'type'     => 'taxonomy_multicheck',
			'taxonomy' => 'post_tag', // Taxonomy Slug
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Checkbox', 'cmb2' ),
			'id'   => $prefix . 'checkbox',
			'type' => 'checkbox',
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test Multi Checkbox', 'cmb2' ),
			'id'      => $prefix . 'multicheckbox',
			'type'    => 'multicheck',
			// 'multiple' => true, // Store values in individual rows
			'options' => array(
				'check1' => __( 'Check One', 'cmb2' ),
				'check2' => __( 'Check Two', 'cmb2' ),
				'check3' => __( 'Check Three', 'cmb2' ),
			),
			// 'inline'  => true, // Toggles display to inline
		) );
		$cmb_demo->add_field( array(
			'name'    => __( 'Test wysiwyg', 'cmb2' ),
			'id'      => $prefix . 'wysiwyg',
			'type'    => 'wysiwyg',
			'options' => array( 'textarea_rows' => 5 ),
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'Test Image', 'cmb2' ),
			'desc' => __( 'Upload an image or enter a URL.', 'cmb2' ),
			'id'   => $prefix . 'image',
			'type' => 'file',
		) );
		$cmb_demo->add_field( array(
			'name'         => __( 'Multiple Files', 'cmb2' ),
			'desc'         => __( 'Upload or add multiple images/attachments.', 'cmb2' ),
			'id'           => $prefix . 'file_list',
			'type'         => 'file_list',
			'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		) );
		$cmb_demo->add_field( array(
			'name' => __( 'oEmbed', 'cmb2' ),
			'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'cmb2' ),
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
