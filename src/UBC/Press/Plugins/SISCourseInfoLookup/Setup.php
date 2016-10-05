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
		$path = apply_filters( 'ubc_press_new_site_path', $path, $dept, $course, $section, $id, $network );

		$new_site_id = wpmu_create_blog( $network_domain, $path, $post->post_title, get_current_user_id() );

		$register_meta_args = array(
			'show_in_rest' => true,
		);
		$register_meta_args = apply_filters( 'ubc_press_course_site_id_meta_args', $register_meta_args );
		register_meta( 'course', 'ubc_press_course_site_id', $register_meta_args );

		// Now associate this Course Post with the new blog ID
		add_post_meta( $id, 'ubc_press_course_site_id', $new_site_id );

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

}
