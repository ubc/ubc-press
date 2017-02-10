<?php

namespace UBC\Press\Plugins\SISCourseInfoLookup;

/**
 * Setup for our UBC SIS Course Info Lookup mods. When a
 * Course post type is published on the main site, we create a new site
 * on that network.
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage SISCourseInfoLookup
 *
 */

class Setup {

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

		// When a Course post is published, we crate a course site on the network we're on
		add_action( 'publish_course', array( $this, 'publish_course__create_course_site' ), 10, 2 );

		// When a new site is created, we need to run the Gravity Forms installer on that site
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog__run_gforms_installer' ), 10, 6 );

		// Register course details as site meta (options)
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog__add_course_meta_to_site' ), 30, 6 );

		// Set up roles
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog__setup_roles' ), 40, 6 );

		// Add students/instructor to new site
		add_action( 'ubc_press_after_publish_course_and_associate_meta', array( $this, 'ubc_press_after_publish_course_and_associate_meta__add_users' ), 10, 3 );

		// Once the gForms installer has run, we need to create the feedback form
		add_action( 'ubc_press_after_publish_course_and_associate_meta', array( $this, 'ubc_press_after_publish_course_and_associate_meta__create_feedback_form' ), 20, 3 );

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

	}/* setup_filters() */


	/**
	 * When a Course post is published, we go ahead and create a course site on the
	 * network. We also add the person who's just published this course as the admin
	 * for that site.
	 *
	 * Additionally, we send a notification to use superadmins.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function publish_course__create_course_site( $id, $post ) {

		// Sanitize
		$id = absint( $id );
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		// Some pieces we'll need from the network
		$network = get_network();
		$network_id = $network->id;
		$network_domain = $network->domain;

		// The site URL ($path) is $dept-$course-$section
		$dept 		= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_department'] );
		$course 	= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_course'] );
		$section	= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_section'] );

		// Check we have all three
		if ( ! $dept || ! $course || ! $section ) {
			return;
		}

		$path = $dept . '-' . $course . '-' . $section;
		$path = apply_filters( 'ubc_press_new_site_path', trailingslashit( \UBC\Helpers::leadingslashit( $path ) ), $dept, $course, $section, $id, $network );

		$new_site_id = wpmu_create_blog( $network_domain, $path, $post->post_title, get_current_user_id(), null, $network_id );

		$register_meta_args = array(
			'show_in_rest' => true,
		);
		$register_meta_args = apply_filters( 'ubc_press_course_site_id_meta_args', $register_meta_args );
		register_meta( 'course', 'ubc_press_course_site_id', $register_meta_args );

		// Now associate this Course Post with the new blog ID
		add_post_meta( $id, 'ubc_press_course_site_id', $new_site_id );

		// Run an action here so we can go ahead and add students/instructor separately
		do_action( 'ubc_press_after_publish_course_and_associate_meta', $id, $post, $new_site_id );

	}/* publish_course__create_course_site() */


	/**
	 * When a new site is created from the Courses list, it's done so programatically.
	 * This means that we'll need to run some initialization pieces such as the gForms
	 * update. This method handles that.
	 *
	 * @since 1.0.0
	 *
	 * @param int	$blog_id Blog ID.
	 * @param int	$user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path	Site path.
	 * @param int	$site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta	Meta data. Used to set initial site options.
	 * @return null
	 */

	public function wpmu_new_blog__run_gforms_installer( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		switch_to_blog( $blog_id );
		\GFForms::setup();
		restore_current_blog();

	}/* wpmu_new_blog__run_gforms_installer() */


	/**
	 * When a new blog is registered, grab the details about the course and add as an option so
	 * we can access that data on the site.
	 *
	 * @since 1.0.0
	 *
	 * @param int	$blog_id Blog ID.
	 * @param int	$user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path	Site path.
	 * @param int	$site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta	Meta data. Used to set initial site options.
	 * @return null
	 */

	public function wpmu_new_blog__add_course_meta_to_site( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		$dept 		= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_department'] );
		$course 	= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_course'] );
		$section	= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_section'] );
		$year		= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_year'] );
		$session	= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_session'] );
		$campus		= sanitize_text_field( $_POST['ubc_sis_course_info_lookup_campus'] );

		$ubc_press_course_details = array(
			'department' => $dept,
			'course' => $course,
			'section' => $section,
			'year' => $year,
			'session' => $session,
			'campus' => $campus,
		);

		switch_to_blog( $blog_id );
		update_option( 'ubc_press_course_details', $ubc_press_course_details );
		restore_current_blog();

	}/* wpmu_new_blog__add_course_meta_to_site() */


	/**
	 * When a new site is created from the Courses list, it's done so programatically.
	 * After it's created we run the gForm installer (in wpmu_new_blog__run_gforms_installer())
	 * After that's been done we need to create a feedback form which is shown when a student
	 * completes all components in all sub-sections of a section.
	 *
	 * This form has multiple questions, including asking about the usefullness of the content
	 * of that section for particular program objectives. The form will list ALL of the P/Os
	 * and then hide the ones not applicable to this section.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */

	public function ubc_press_after_publish_course_and_associate_meta__create_feedback_form( $id, $post, $blog_id ) {

		$form_array = array();

		$form_array['title'] = 'Content Feedback';
		$form_array['description'] = 'Do not delete this form.

It is shown when a student completes all components for a section.';

		$form_array['labelPlacement'] = 'top_label';
		$form_array['descriptionPlacement'] = 'below';
		$form_array['button'] = array(
			'type' => 'text',
			'text' => 'Submit Feedback',
			'imageUrl' => '',
		);

		$form_array['date_created'] = date( 'Y-m-d H:i:s' );
		$form_array['is_active'] = true;
		$form_array['cssClass'] = 'ubc-press-content-feedback-form';
		$form_array['requireLogin'] = true;
		$form_array['useCurrentUserAsAuthor'] = true;

		// Confirmations
		$conf_id = \UBC\Press\Utils::random_string_of_length( 13 );
		$form_array['confirmations'] = array(
			$conf_id => array(
				'id' 		=> $conf_id,
				'name' 		=> 'Default Confirmation',
				'isDefault'	=> 1,
				'type' 		=> 'message',
				'message' 	=> 'Thank you for providing feedback.',
			),
		);

		// We have several yes/no fields, re-use this for the choices
		$yes_no_choices = array(
			array( 'text' => 'Yes', 'value' => 'Yes' ),
			array( 'text' => 'No', 'value' => 'No' ),
		);

		$form_array['fields'] = array(
			array(
				'id' => 1,
				'content' => 'Did you find this content:',
				'type' => 'html',
				'isRequired' => false,
				'size' => 'medium',
				'cssClass' => 'ubc-press-did-you-find-this-content-label',
			),
			array(
				'id' => 2,
				'label' => 'Engaging?',
				'type' => 'radio',
				'cssClass' => 'ubc-press-engaging-yes-no',
				'choices' => $yes_no_choices,
			),
			array(
				'id' => 3,
				'label' => '(Optional) Why did you not find the content engaging? How could it be improved?',
				'type' => 'textarea',
				'cssClass' => 'ubc-press-engaging-feedback',
				'conditionalLogic' => array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' => array(
						array(
							'fieldId' => 2,
							'operator' => 'is',
							'value' => 'No',
						),
					),
				),
			),
			array(
				'id' => 4,
				'label' => 'Concise?',
				'type' => 'radio',
				'cssClass' => 'ubc-press-concise-yes-no',
				'choices' => $yes_no_choices,
			),
			array(
				'id' => 5,
				'label' => '(Optional) Why was the content not concise enough?',
				'type' => 'textarea',
				'cssClass' => 'ubc-press-concise-feedback',
				'conditionalLogic' => array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' => array(
						array(
							'fieldId' => 4,
							'operator' => 'is',
							'value' => 'No',
						),
					),
				),
			),
			array(
				'id' => 6,
				'label' => 'Clear?',
				'type' => 'radio',
				'cssClass' => 'ubc-press-clear-yes-no',
				'choices' => $yes_no_choices,
			),
			array(
				'id' => 7,
				'label' => '(Optional) What was not clear about this content?',
				'type' => 'textarea',
				'cssClass' => 'ubc-press-clear-feedback',
				'conditionalLogic' => array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' => array(
						array(
							'fieldId' => 6,
							'operator' => 'is',
							'value' => 'No',
						),
					),
				),
			),
			array(
				'id' => 8,
				'label' => 'Well Presented?',
				'type' => 'radio',
				'cssClass' => 'ubc-press-well-presented-yes-no',
				'choices' => $yes_no_choices,
			),
			array(
				'id' => 9,
				'label' => '(Optional) How could the presentation of this content be improved?',
				'type' => 'textarea',
				'cssClass' => 'ubc-press-presentation-feedback',
				'conditionalLogic' => array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' => array(
						array(
							'fieldId' => 8,
							'operator' => 'is',
							'value' => 'No',
						),
					),
				),
			),
			array(
				'id' => 10,
				'label' => 'Section Break',
				'type' => 'section',
				'cssClass' => 'ubc-press-section-break',
			),
			array(
				'id' => 11,
				'label' => 'LO Title',
				'type' => 'html',
				'cssClass' => 'ubc-press-html',
				'content' => 'Did this content help you better understand:',
			),
		);

		// Now we add 2 fields for each learning objective. One for the 'did it help you'
		// And one for the free-form text area used to give feedback if the answer is 'no'
		switch_to_blog( $blog_id );
		$programs = absint( $_POST['ubc_course_to_programs_program_association'][0] );
		$learning_objectives = \UBC\Press\Utils::get_program_objectives_for_post_of_site( $programs, \UBC\Press\Utils::get_id_for_networks_main_site_of_blog_id( $blog_id ) );

		// If there are no LOs then bail
		if ( empty( $learning_objectives ) || ! is_array( $learning_objectives ) ) {
			$result = \GFAPI::add_form( $form_array );
			restore_current_blog();
			return;
		}

		// OK we have LOs so we add a radio field and textarea for each one.
		// $id starts are 12 because we've already got a field with id => 11 above
		$id = 12;
		$count = 1;
		foreach ( $learning_objectives as $term_id => $term ) {

			$form_array['fields'][] = array(
				'id' => $id,
				'label' => 'Did this content help you better understand ' . $term->name . '?',
				'type' => 'radio',
				'cssClass' => 'ubc-press-lo-yes-no-' . $count,
				'choices' => $yes_no_choices,
			);

			$feedback_id = $id + 1;

			$form_array['fields'][] = array(
				'id' => $feedback_id,
				'label' => '(Optional) How could the content be improved to help you better understand ' . $term->name . '?',
				'type' => 'textarea',
				'cssClass' => 'ubc-press-lo-feedback-' . $count,
				'conditionalLogic' => array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' => array(
						array(
							'fieldId' => $id,
							'operator' => 'is',
							'value' => 'No',
						)
					),
				),
			);

			$id = $id + 2;
			$count = $count + 1;

		}

		$result = \GFAPI::add_form( $form_array );

		// Now to make it easier to grab this form when we need it, add an option to save this new form ID
		add_option( 'ubc_press_feedback_form_id', $result, null, 'no' );
		restore_current_blog();
		return;

	}/* ubc_press_after_publish_course_and_associate_meta__create_feedback_form() */

	/**
	 * We have several custom roles, when a new site is created, ensure the roles are set up.
	 *
	 * @since 1.0.0
	 *
	 * @param int	$blog_id Blog ID.
	 * @param int	$user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path	Site path.
	 * @param int	$site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta	Meta data. Used to set initial site options.
	 * @return null
	 */

	public function wpmu_new_blog__setup_roles( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		switch_to_blog( $blog_id );
		$roles = new \UBC\Press\Roles\Setup;
		$roles->init();
		restore_current_blog();

	}/* wpmu_new_blog__setup_roles() */


	/**
	 * When a new course post is published, right at the end we have an action after all meta is
	 * associated and the site has been created. We hook in here to go and add instructor(s) and
	 * students to this newly created site.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function ubc_press_after_publish_course_and_associate_meta__add_users( $id, $post, $new_site_id ) {

		switch_to_blog( $new_site_id );
		$ubc_press_course_details = get_option( 'ubc_press_course_details' );
		restore_current_blog();

		$instructor = sanitize_text_field( $_POST['ubc_sis_course_info_lookup_instructor'] );

		// Add instructor to this site
		$instructor_id = \UBC\Press\ELDAP\Utils::create_user_and_add_eldap_properties( $instructor );

		add_user_to_blog( $new_site_id, $instructor_id, 'instructor' );

		// Now add students
		$data = array( 'dept' => $ubc_press_course_details['department'], 'course' => $ubc_press_course_details['course'], 'section' => $ubc_press_course_details['section'], 'year' => $ubc_press_course_details['year'], 'session' => $ubc_press_course_details['session'], 'campus' => $ubc_press_course_details['campus'] );
		$cn = \UBC\Press\ELDAP\Utils::get_cn_for_section( $data );

		// And use that cn to get the classlist from ELDAP
		$class_list = \UBC\Press\ELDAP\Utils::get_classlist_for_section( $cn );

		foreach ( $class_list as $id => $username ) {
			$user_id = \UBC\Press\ELDAP\Utils::create_user_and_add_eldap_properties( $username );
			add_user_to_blog( $new_site_id, $user_id, 'student' );
		}

	}/* ubc_press_after_publish_course_and_associate_meta__add_users() */

}
