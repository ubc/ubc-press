<?php

namespace UBC\Press;

/**
 * Static utility methods used across multiple different classes
 * Will eventually be placed in a separate plugin which can be
 * required in composer
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Utils
 *
 */

class Utils {

	/**
	 * The current user object
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (object) $user
	 */

	public static $user = null;


	/**
	 * A map of component type to its associated icon
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (object) $component_icons
	 */

	public static $component_icons = array(
		'AddLectureWidget' 			=> 'dashicons-megaphone',
		'AddAssignmentWidget'		=> 'dashicons-media-text',
		'AddHandoutWidget' 			=> 'dashicons-portfolio',
		'note' 						=> 'dashicons-edit',
		'AddReadingWidget' 			=> 'dashicons-book-alt',
		'AddQuizWidget' 			=> 'dashicons-awards',
		'AddLinkWidget' 			=> 'dashicons-admin-links',
		'AddDiscussionForumWidget'	=> 'dashicons-format-status',
	);


	/**
	 * Fetch the component icons array
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return array A map of component type to its associated icon
	 */

	public static function get_component_icons() {
		return apply_filters( 'ubc_press_get_component_icons', static::$component_icons );
	}/* get_component_icons() */


	/**
	 * Get the icon for a spcific component type
	 *
	 * Usage: \UBC\Press\Utils::get_component_icon( 'assignment' );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $component_type - The component type for which we need the icon
	 * @return (string) The associated icon
	 */

	public static function get_component_icon( $component_type = false ) {

		$all_icons = static::get_component_icons();
		return apply_filters( 'ubc_press_get_component_icon', $all_icons[ $component_type ] );

	}/* get_component_icon() */



	/**
	 * Get the current user object or false if a user is not logged in
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return object|false - The current user's WP_User object or false if not logged in
	 */

	public static function get_current_user() {

		// If a user is not logged in, ensure the class's property is empty and bail
		if ( ! is_user_logged_in() ) {
			static::$user = null;
			return false;
		}

		// If we already have a user object, return that
		if ( ! empty( static::$user ) && is_a( static::$user, 'WP_User' ) ) {
			return static::$user;
		}

		$user = wp_get_current_user();

		return apply_filters( 'ubc_press_utils_get_current_user', $user );

	}/* get_current_user() */



	/**
	 * Check if the current user has specified role
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $role - The role which we are checking the current user for
	 * @return (bool)
	 */

	public static function current_user_has_role( $role = '' ) {

		if ( empty( $role ) ) {
			return false;
		}

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = static::get_current_user();

		if ( ! is_a( $user, 'WP_User' ) ) {
			return false;
		}

		// Sanitize and check that the passed role actually exists
		$role = sanitize_text_field( $role );
		$role_exists = get_role( $role );

		if ( ! is_a( $role_exists, 'WP_Role' ) ) {
			return false;
		}

		// OK, role exists, we have a user object, compare
		if ( ! in_array( $role, (array) $user->roles ) ) {
			return false;
		}

		return apply_filters( 'ubc_press_utils_current_user_has_role', true, $role, $user );

	}/* current_user_has_role() */



	/**
	 * Check if the current user's role is in one of an array of roles
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $roles - The roles to check
	 * @return (bool)
	 */

	public static function current_users_role_is_one_of( $roles = array() ) {

		if ( empty( $roles ) ) {
			return false;
		}

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = static::get_current_user();

		if ( ! is_a( $user, 'WP_User' ) ) {
			return false;
		}

		// Assume false, change to true if the user's role is found in the passed $roles
		$user_role_in_roles = false;

		$user_roles = (array) $user->roles;

		foreach ( $user_roles as $key => $role ) {

			if ( in_array( $role, array_values( $roles ) ) ) {
				$user_role_in_roles = true;
				break;
			}
		}

		return apply_filters( 'ubc_press_utils_current_users_role_is_one_of', $user_role_in_roles, $user, $roles );

	}/* current_users_role_is_one_of() */


	/**
	 * Get content for a handout
	 *
	 * Usage: \UBC\Press\Utils::get_handout_content( $post_id )
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - A specific ID for a handout to fetch
	 * @param (array) $fields_to_fetch - A specific set of fields to fetch
	 * @param (array) $taxonomies_to_fetch - A specific set of taxonomies to fetch
	 * @return (array) An array of content, by meta key
	 */

	public static function get_handout_content( $post_id = null, $fields_to_fetch = array(), $taxonomies_to_fetch = array() ) {

		// If we haven't specified which fields to fetch, get the default
		if ( empty( $fields_to_fetch ) ) {
			$fields_to_fetch = array(
				'_handout_details_file_list',
				'_handout_details_description',
			);
		}

		if ( empty( $taxonomies_to_fetch ) ) {
			$taxonomies_to_fetch = array(
				'handout_type',
			);
		}

		return apply_filters( 'ubc_press_handout_content', static::get_generic_content( $post_id, $fields_to_fetch, $taxonomies_to_fetch ) );

	}/* get_handout_content() */



	/**
	 * Get the content for a link
	 *
	 * Usage: \UBC\Press\Utils::get_link_content( $post_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - A specific ID for a handout to fetch
	 * @return (array) An array of content
	 */

	public static function get_link_content( $post_id = null ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$post_id = absint( $post_id );

		$meta_key = '_link_details_link_details_group';

		$links = get_post_meta( $post_id, $meta_key, true );

		return $links;

	}/* get_link_content() */



	/**
	 * Fetch the content for a section, including the meta (description etc.)
	 * and the content blocks (components)
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - A specific ID for a section to fetch
	 * @param (array) $fields_to_fetch - A specific set of fields to fetch
	 * @param (array) $taxonomies_to_fetch - A specific set of taxonomies to fetch
	 * @return (array) An array of content, by meta key
	 */

	public static function get_section_content( $post_id, $fields_to_fetch = array(), $taxonomies_to_fetch = array() ) {

		// If we haven't specified which fields to fetch, get the default
		if ( empty( $fields_to_fetch ) ) {
			$fields_to_fetch = array(
				'_section_description_content',
			);
		}

		// if ( empty( $taxonomies_to_fetch ) ) {
		// 	$taxonomies_to_fetch = array(
		// 		'handout_type',
		// 	);
		// }

		return apply_filters( 'ubc_press_section_content', static::get_generic_content( $post_id, $fields_to_fetch, $taxonomies_to_fetch ) );

	}/* get_section_content() */



	/**
	 * A generic method which allows us to fetch fields and taxonomies for a specific post
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - A specific ID for a section to fetch
	 * @param (array) $fields_to_fetch - A specific set of fields to fetch
	 * @param (array) $taxonomies_to_fetch - A specific set of taxonomies to fetch
	 * @return (array) An array of content, by meta key
	 */

	public static function get_generic_content( $post_id, $fields_to_fetch = array(), $taxonomies_to_fetch = array() ) {

		// Default to the ID in the loop if none passed
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( empty( $post_id ) ) {
			return array();
		}

		// Sanitize
		$post_id = absint( $post_id );

		// Start our output
		$content = array();
		$content['fields'] = array();
		$content['taxonomies'] = array();

		// Add each field
		foreach ( $fields_to_fetch as $id => $field ) {
			$content['fields'][ $field ] = get_post_meta( $post_id, $field, true );
		}

		// Add each taxonomy and it's terms
		foreach ( $taxonomies_to_fetch as $id => $taxonomy ) {
			$tax_terms = wp_get_object_terms( $post_id, $taxonomy );
			$content['taxonomies'][ $taxonomy ] = $tax_terms;
		}

		return apply_filters( 'ubc_press_generic_content', $content, $post_id );

	}/* get_generic_content() */



	/**
	 * An array of faculties
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (array) an array of faculties
	 */

	public static function get_faculty_list() {

		$faculties = array(
			'' 									=> 'Select Faculty or none',
			'applied-science' 	=> 'Faculty of Applied Science',
			'arts' 							=> 'Faculty of Arts',
			'health-discplines' => 'College of Health Disciplines',
			'dentistry' 				=> 'Faculty of Dentistry',
			'education' 				=> 'Faculty of Education',
			'forestry' 					=> 'Faculty of Forestry',
			'grad-studies' 			=> 'Graduate Studies and Post-doctoral Studies',
			'mental-health' 		=> 'Institute of Mental Health',
			'land-food' 				=> 'Faculty of Land and Food System',
			'law' 							=> 'Peter A. Allard School of Law',
			'medicine' 					=> 'Faculty of Medicine',
			'president' 				=> 'Office of the President',
			'pharm-sci' 				=> 'Faculty of Pharmaceutical Sciences',
			'sauder' 						=> 'Sauder School of Business',
			'science' 					=> 'Faculty of Science',
			'vantage' 					=> 'Vantage College',
			'vp-academic' 			=> 'VP Academic and Provost Office',
			'vp-ccp' 						=> 'VP Campus and Community Planning',
			'vp-dae' 						=> 'VP Development and Alumni Engagement',
			'vp-fro' 						=> 'VP Finance Resources and Operations',
			'vp-hr' 						=> 'VP Human Resources',
			'vp-ri' 						=> 'VP Research and International',
			'vp-students' 			=> 'VP Students',
		);

		return apply_filters( 'ubc_press_faculities_list', $faculties );

	}/* get_faculty_list() */



	/**
	 * An array of departments. This is an associative array of arrays.
	 * The top-level key is the faculty
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (array) An associative array of faculties
	 */

	public static function get_department_list() {

		$departments = array(

			'arts' => array(
				'philosophy' => 'Philosophy',
				'drama' => 'Drama',
			),

			'medicine' => array(
				'nursing' => 'Nursing',
				'midwifery' => 'Midwifery',
			),

			'sauder' => array(
				'sauder' => 'Sauder',
			),

		);

		return apply_filters( 'ubc_press_departments_list', $departments );

	}/* get_department_list() */


	/**
	 * Sanitize a date only allowing 0-9 and a forward slash
	 * Usage: \UBC\Press\Utils::sanitize_date( $date );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $date - The date to sanitize
	 * @return (string) Sanitized date
	 */

	public static function sanitize_date( $date ) {

		// Store a reference to what is passed in to pass to filter
		$_date = $date;

		$date = preg_replace( '([^0-9/])', '', $date );

		/**
		 * Filters a sanitized date
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $date - The sanitized date
		 * @param (string) $_date - The date that was passed in to the method
		 */

		return apply_filters( 'ubc_press_sanitized_date', $date, $_date );

	}/* sanitize_date() */


	/**
	 * Sanitize a time only allowing 0-9, the letters a, p and m,
	 * a colon and a space
	 * Usage: \UBC\Press\Utils::sanitize_time( $time );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $time - The time to sanitize
	 * @return (string) Sanitized time
	 */

	public static function sanitize_time( $time ) {

		// Store a reference to what is passed in to pass to filter
		$_time = $time;

		$time = preg_replace( '[^ampAMP:0-9\s]', '', $time );

		/**
		 * Filters a sanitized time
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $time - The sanitized time
		 * @param (string) $_time - The time that was passed in to the method
		 */

		return apply_filters( 'ubc_press_sanitized_time', $time, $_time );

	}/* sanitize_time() */

	/**
	 * Generic method to retrieve a specific value from the specified key
	 * in the passed in array
	 * Usage: \UBC\Press\Utils::get_data_from_post( $key, $post );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $key - The array key we want to get the value for
	 * @param (array) $post - The array ($_POST) from which the $key resides
	 * @return (mixed) The *unsanitized* value of $post[$key] if it exists
	 */

	public static function get_data_from_post( $key = false, $post = array() ) {

		// Bail early if no key set
		if ( ! $key ) {
			return false;
		}

		// If $post isn't an array, go away
		if ( ! is_array( $post ) ) {
			return false;
		}

		// And again if $post is empty
		if ( empty( $post ) ) {
			return false;
		}

		// And if $key is not a key in $post, bail
		if ( ! array_key_exists( $key, $post ) ) {
			return false;
		}

		$value = $post[ $key ];

		/**
		 * Filters a value returned from a $post array
		 *
		 * @since 1.0.0
		 *
		 * @param (mixed) $value - The value of $post[ $key ]
		 * @param (string) $key - The key in the $post array we looked for
		 * @param (array) $post - The array containing $key
		 */

		return apply_filters( 'ubc_press_get_data_from_post', $value, $key, $post );

	}/* get_data_from_post() */


	/**
	 * Test if the passed string is a timestamp
	 * Usage: \UBC\Press\Utils::is_timestamp( $timestamp );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $timestamp - The string to test if it's a timestamp or not
	 * @return (bool) True if $timestamp is a timestamp. False otherwise.
	 */

	public static function is_timestamp( $timestamp ) {

		if ( ctype_digit( $timestamp ) && strtotime( date( 'Y-m-d H:i:s', $timestamp ) ) === (int) $timestamp ) {
			return true;
		} else {
			return false;
		}

	}/* is_timestamp() */


	/**
	 * Get course objectives.
	 * "Usable" means return an array where the keys are the term IDs
	 * and the values are the term names
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) $usable - Whether this should be a simple term_id=>term_name array
	 * @return (array) An array of terms
	 */

	public static function get_course_objectives( $usable = false ) {

		$course_objectives = get_terms( 'course-objective', array( 'hide_empty' => 0 ) );

		if ( false === $usable ) {
			return $course_objectives;
		}

		// If it's empty, bail early with an empty array
		if ( empty( $course_objectives ) ) {
			return array();
		}

		$usable_course_objectives = array();
		foreach ( $course_objectives as $id => $term_details ) {
			if ( ! is_object( $term_details ) ) {
				continue;
			}
			$usable_course_objectives[ $term_details->term_id ] = $term_details->name;
		}

		return $usable_course_objectives;

	}/* get_course_objectives */


	/**
	 * Program objectives are set at the network level. When a new network is created
	 * That network has a 'main site'. That main site has a CPT called programs. And
	 * those programs have a taxonomy called program-objectives. When a course site
	 * is created, we ask them what program the course belongs to. This allows us to
	 * know which program objectives are available at a course level
	 *
	 * @since 1.0.0
	 *
	 * @param bool) $usable - Whether this should be a simple term_id=>term_name array
	 * @return (array) An array of terms
	 */

	public static function get_program_objectives( $usable = false ) {

		// First we need to get the main site for the network of the current blog
		$main_site_id 	= \UBC\Press\Utils::get_id_for_networks_main_site_of_blog_id( get_current_blog_id() );

		// The course site ID('course' post type) is stored as the post ID where on the main
		// site, the meta_key ubc_press_course_site_id is equal to the current blog ID
		// Getting that ID allows us to search the Program Association and then search that
		// To get the terms
		$current_blog_id = get_current_blog_id();

		global $wpdb;

		$blog_prefix	= $wpdb->get_blog_prefix( $main_site_id );

		// First get the post_id for the course post on the network's main site
		$query			= "SELECT post_id FROM {$blog_prefix}postmeta WHERE meta_key = %s AND meta_value = %d";
		$query 			= $wpdb->prepare( $query, 'ubc_press_course_site_id', $current_blog_id );
		$post_id_of_course_site = $wpdb->get_var( $query );

		// Now get the ubc_course_to_programs_program_association for this post
		$query			= "SELECT meta_value FROM {$blog_prefix}postmeta WHERE meta_key = %s AND post_id = %d";
		$query 			= $wpdb->prepare( $query, 'ubc_course_to_programs_program_association', $post_id_of_course_site );
		$program_id 	= $wpdb->get_var( $query );
		$program_id		= unserialize( $program_id );

		if ( false === $program_id ) {
			return array();
		}

		// Now we have that, we can look at the terms assoc
		$program_objectives = static::get_program_objectives_for_post_of_site( $program_id[0], $main_site_id );

		if ( false === $usable ) {
			return $program_objectives;
		}

		$usable_program_objectives = array();

		foreach ( $program_objectives as $id => $term_obj ) {
			$usable_program_objectives[ $term_obj->term_id ] = $term_obj->name;
		}

		return $usable_program_objectives;

	}/* get_program_objectives() */


	/**
	 * Fetch the program objectives for a post on the given site.
	 * Programs are a post type on the main network site. Program objectives are
	 * a taxonomy of that post type
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post for which we're looking for the associated terms
	 * @param (int) $site_id - The site on which this post lives (will be a main site ID)
	 * @return (array|false) - An array of terms (program objectives)
	 */

	public static function get_program_objectives_for_post_of_site( $post_id, $site_id ) {

		// Now fetch a list of programs from the main site
		global $wpdb;

		$post_id = absint( $post_id );
		$site_id = absint( $site_id );

		$blog_prefix	= $wpdb->get_blog_prefix( $site_id );

		// First get the term_tax_ids for this post
		$query			= "SELECT term_taxonomy_id FROM {$blog_prefix}term_relationships WHERE object_id = %d";
		$query 			= $wpdb->prepare( $query, $post_id );
		$term_tax_ids 	= $wpdb->get_results( $query );

		if ( empty( $term_tax_ids ) ) {
			return array();
		}

		// Now we know the term IDs, so let's find out what they are
		$usable_term_ids = array();
		foreach ( $term_tax_ids as $id => $term_obj ) {
			$usable_term_ids[] = $term_obj->term_taxonomy_id;
		}

		$num_terms = count( $usable_term_ids );
		// prepare the right amount of placeholders
		$placeholders = array_fill( 0, $num_terms, '%d' );

		// glue together all the placeholders...
		// $format = '%d, %d, %d, %d, %d, [...]'
		$format = implode( ', ', $placeholders );

		$query			= "SELECT * FROM {$blog_prefix}terms WHERE term_id IN ({$format})";
		$query			= $wpdb->prepare( $query, $usable_term_ids );
		$terms 			= $wpdb->get_results( $query );

		return $terms;

	}/* get_program_objectives_for_post_of_site() */


	/**
	 * During the onboarding process we ask what program the new site is for. Programs
	 * are a CPT on the network's main site. We grab a list of 'em
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) $usable Whether this should be a simple post_id=>post_title array
	 * @return (array) Array of Objects of programs available on the network
	 */

	public static function get_programs_for_current_site( $usable = false ) {

		// First we need to get the main site for the network of the current blog
		$main_site_id 	= \UBC\Press\Utils::get_id_for_networks_main_site_of_blog_id( get_current_blog_id() );

		// Now fetch a list of programs from the main site
		global $wpdb;

		$blog_prefix	= $wpdb->get_blog_prefix( $main_site_id );

		$query			= "SELECT * FROM {$blog_prefix}posts WHERE post_type = %s AND post_status = %s";
		$query 			= $wpdb->prepare( $query, 'program', 'publish' );
		$programs 		= $wpdb->get_results( $query );

		if ( empty( $programs ) ) {
			return array();
		}

		if ( false === $usable ) {
			return $programs;
		}

		$usable_programs = array();

		foreach ( $programs as $id => $program_object ) {
			$usable_programs[ $program_object->ID ] = $program_object->post_title;
		}

		return $usable_programs;

	}/* get_programs_for_current_site() */


	/**
	 * Get the ID of the main site of the network in which the passed
	 * $blog_id resides
	 *
	 * Usage: \UBC\Press\Utils::get_id_for_networks_main_site_of_blog_id( get_current_blog_id() );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $blog_id - The ID of the blog for which we are looking for the network's main site ID
	 * @return (int|false) The ID of the network's main site. False on failure.
	 */

	public static function get_id_for_networks_main_site_of_blog_id( $blog_id = false ) {

		// get_main_site_for_network() from WPMN is required
		if ( ! function_exists( 'get_main_site_for_network' ) ) {
			return false;
		}

		// If $blog_id isn't passed, default to the current blog ID
		if ( false === $blog_id ) {
			$blog_id = get_current_blog_id();
		}

		// Sanitize
		$blog_id = (int) $blog_id;

		// If sanitization fails, you're naughty, bail
		if ( empty( $blog_id ) ) {
			return false;
		}

		// Check object cache
		if ( ! $main_site_id = wp_cache_get( 'network:' . $blog_id . ':main_site_for_blog', 'site-options' ) ) {

			global $wpdb;

			// Fetch the site_id (network ID) for the passed blog_id
			$query = "SELECT site_id FROM $wpdb->blogs WHERE blog_id = %d LIMIT 1";
			$query = $wpdb->prepare( $query, $blog_id );
			$network_id = $wpdb->get_var( $query );

			// Yeah, well, this should never happen, but JIC
			if ( empty( $network_id ) ) {
				return false;
			}

			// Now use WPMS's get_main_site_for_network() to get the main site for this network
			$main_site_id = get_main_site_for_network( $network_id );

			// Prime the cache
			wp_cache_add( 'network:' . $blog_id . ':main_site_for_blog', $main_site_id, 'site-options' );

		}

		// 🚢
		return (int) $main_site_id;

	}/* get_id_for_networks_main_site_of_blog_id() */



	/**
	 * Test if the current site is the main site for the network
	 * Usage: \UBC\Press\Utils::current_site_is_main_site_for_network();
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (bool) true if the current site is the network's main site. False otherwise
	 */

	public static function current_site_is_main_site_for_network() {

		$current_blog_id = get_current_blog_id();
		$main_site_id = \UBC\Press\Utils::get_id_for_networks_main_site_of_blog_id( $current_blog_id );

		if ( $current_blog_id === $main_site_id ) {
			return true;
		}

		return false;

	}/* current_site_is_main_site_for_network() */


	/**
	 * Determine if a component is marked as complete for the passed user ID
	 * Each user has a meta-key 'ubc_press_completed'. They keys in the outer array
	 * are the site IDs. The keys in each inner array
	 * are the user ID and the values are an array of details, right now just the
	 * timestamp of when it was marked as complete.
	 *
	 * Usage: \UBC\Press\Utils::component_is_completed( $post_id, $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post that holds this component
	 * @param (int) $user_id - The ID of the user for whom we're checking if this component is complete
	 * @return (bool) True if this user has marked this component as complete, false otherwise
	 */

	public static function component_is_completed( $post_id, $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// i.e. array( '1' => array( 23 => array( 'when' => 345676543 ), 54 => array( 'when' => 34567123 ) ) )
		$completed = \UBC\Press\Utils::get_completed_components_for_user( $user_id );

		if ( ! is_array( $completed ) ) {
			$completed = array();
		}

		if ( ! array_key_exists( $site_id, $completed ) ) {
			return false;
		}

		if ( array_key_exists( $post_id, $completed[ $site_id ] ) ) {
			return true;
		} else {
			return false;
		}

	}/* component_is_completed() */


	/**
	 * Mark a component as being completed by the passed user.
	 * Adds to user meta 'ubc_press_completed'. If already exists, will
	 * override the set timestamp
	 *
	 * Usage: \UBC\Press\Utils::set_component_as_complete( $post_id, $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post that holds this component
	 * @param (int) $user_id - The ID of the user for whom we're marking as complete
	 * @return (bool) The return of update_post_meta()
	 */

	public static function set_component_as_complete( $post_id, $user_id ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = \UBC\Press\Utils::get_completed_components_for_user( $user_id );

		// Ensure it's an array
		if ( ! is_array( $current_completed ) ) {
			$current_completed = array();
		}

		if ( ! array_key_exists( $site_id, $current_completed ) ) {
			$current_completed[ $site_id ] = array();
		}

		// Add our completion
		$current_completed[ $site_id ][ $post_id ] = array( 'when' => time() );

		do_action( 'ubc_press_set_component_as_complete', $post_id, $user_id );

		return (bool) update_user_meta( $user_id, 'ubc_press_completed', $current_completed );

	}/* set_component_as_complete() */


	/**
	 * Mark a component as incomplete.
	 *
	 * Removes the key/value pair from the user meta for the component requested
	 *
	 * Usage: \UBC\Press\Utils::set_component_as_incomplete( $post_id, $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post that holds this component
	 * @param (int) $user_id - The ID of the user for whom we're marking as complete
	 * @return (bool) The return of update_post_meta()
	 */

	public static function set_component_as_incomplete( $post_id, $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = \UBC\Press\Utils::get_completed_components_for_user( $user_id );

		// Ensure it's an array
		if ( ! is_array( $current_completed ) ) {
			$current_completed = array();
		}

		if ( ! array_key_exists( $site_id, $current_completed ) ) {
			$current_completed[ $site_id ] = array();
		}

		if ( array_key_exists( $post_id, $current_completed[ $site_id ] ) ) {
			unset( $current_completed[ $site_id ][ $post_id ] );
		}

		do_action( 'ubc_press_set_component_as_incomplete', $post_id, $user_id );

		return (bool) update_user_meta( $user_id, 'ubc_press_completed', $current_completed );

	}/* set_component_as_incomplete() */


	/**
	 * Retrieve WHEN a user completed a component.
	 *
	 * Looks at the post meta for the specified post ID and user ID, then
	 * fetches the 'when' key of the sub-array
	 *
	 * Usage: \UBC\Press\Utils::get_when_component_was_completed( $post_id, $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post that holds this component
	 * @param (int) $user_id - The ID of the user for whom we're marking as complete
	 * @return (string) The timestamp of when this component was completed
	 */

	public static function get_when_component_was_completed( $post_id, $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = \UBC\Press\Utils::get_completed_components_for_user( $user_id );

		if ( ! isset( $current_completed[ $site_id ] ) || ! isset( $current_completed[ $site_id ][ $post_id ] ) ) {
			return '';
		}

		return $current_completed[ $site_id ][ $post_id ]['when'];

	}/* get_when_component_was_completed() */


	/**
	 * A simple getter for the currently completed components for a user
	 *
	 * Usage: \UBC\Press\Utils::get_completed_components_for_user( $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $user_id - The ID of the user for whom we're looking for completed components
	 * @return (array) The completed components for the user
	 */

	public static function get_completed_components_for_user( $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$user_id = absint( $user_id );

		// Current meta saved for this component
		$current_completed = get_user_meta( $user_id, 'ubc_press_completed', true );

		return $current_completed;

	}/* get_completed_components_for_user() */


	/**
	 * Fetch the completed components for a section for the given user
	 *
	 * Usage: \UBC\Press\Utils::completed_components_for_section( $post_id, $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post that holds the components (the section)
	 * @param (int) $user_id - The ID of the user for whom we're checking
	 * @return (array) The completed component IDs for the section including a count of total components in the section
	 */

	public static function completed_components_for_section( $post_id, $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Start our output
		$completed_components = array();
		$completed_components['completed_components'] = array();

		$component_associations = get_post_meta( $post_id, 'component_associations', true );

		// No panels or no widgets therefore no completed components
		if ( empty( $component_associations ) ) {
			$completed_components['total_components'] = 0;
			return $completed_components;
		}

		// The number of total completable components on this section
		$completable_components = array();
		foreach ( $component_associations as $key => $cpost_id ) {
			if ( \UBC\Press\Utils::component_can_be_completed( $cpost_id ) ) {
				$completable_components[] = $cpost_id;
			}
		}

		$completed_components['total_components'] = count( $completable_components );

		// For each of these components, we look in user meta to determine if they're completed
		$user_completed_components = \UBC\Press\Utils::get_completed_components_for_user( $user_id );
		$sites_completed_components = ( is_array( $user_completed_components ) && isset( $user_completed_components[ $site_id ] ) ) ? $user_completed_components[ $site_id ] : false;

		// No completed components for this site
		if ( false === $sites_completed_components ) {
			return $completed_components;
		}

		foreach ( $component_associations as $key => $component_post_id ) {

			if ( empty ( $component_post_id ) ) {
				continue;
			}

			if ( array_key_exists( $component_post_id, $sites_completed_components ) ) {
				$completed_components['completed_components'][] = $component_post_id;
			}
		}

		return $completed_components;

	}/* completed_components_for_section() */



	/**
	 * Some components are 'completable'. This means that a user can "Mark as complete".
	 * Not all components are - mainly because they don't have an associated ID.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (array) A list of completable components
	 */

	public static function get_completable_component_types() {

		$completables = array(
			'assignment',
			'reading',
			'link',
			'lecture',
			'post',
			'page',
			'quiz',
			'hiddenquiz',
			'h5p',
		);

		return apply_filters( 'ubc_press_completable_component_types', $completables );

	}/* get_completable_component_types() */


	/**
	 * A list of components that are automatically (not manually) completable
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (array) A list of automatically completable component types
	 */

	public static function get_automatic_completable_component_types() {

		$automatic_completables = array(
			'quiz',
			'hiddenquiz',
		);

		return apply_filters( 'ubc_press_automatic_completable_component_types', $automatic_completables );

	}/* get_automatic_completable_component_types() */

	/**
	 * A true or false function used to determine whether components use mark as complete
	 *
	 * @since 0.7.2
	 *
	 * @return (bool) True or false.
	 */

	public static function do_components_show_mark_as_complete() {

		return true;

	}/* do_components_show_mark_as_complete */

	/**
	 * Test whether a component can be completed. Expects the Post ID
	 * of a component which is used to determine the type of component.
	 * That type is then compared to the list in get_completable_component_types()
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The post ID of a component
	 * @return (bool) True if the component can be marked as completed. False otherwise.
	 */

	public static function component_can_be_completed( $post_id ) {

		$post_id 		= absint( $post_id );

		$post_type 		= get_post_type( $post_id );

		$completables	= \UBC\Press\Utils::get_completable_component_types();

		if ( in_array( $post_type, $completables ) ) {
			return true;
		} else {
			return false;
		}

	}/* component_can_be_completed() */


	/**
	 * Certain post types, i.e. Quizzes are completed automatically when an action
	 * happens. This means they're not completable 'manually' and shouldn't show
	 * the 'mark as complete' button.
	 *
	 * Usage: \UBC\Press\Utils::component_is_completed_automatically( $post_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The post ID of a component
	 * @return (bool) True if this component has an automatic completion
	 */

	public static function component_is_completed_automatically( $post_id ) {

		$post_id 		= absint( $post_id );

		$post_type 		= get_post_type( $post_id );

		$automatic_completables	= \UBC\Press\Utils::get_automatic_completable_component_types();

		if ( in_array( $post_type, $automatic_completables ) ) {
			return true;
		} else {
			return false;
		}

	}/* component_is_completed_automatically() */


	/**
	 * Our custom widgets stored a post ID, but in a separate field depending on their type
	 *
	 * Usage: \UBC\Press\Utils::get_post_id_from_widget( $widget_type, $panel_widget );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $widget_type - The type of widget
	 * @param (array) $panel_widget - the widget we're looking through
	 * @return (int|false) The Post ID associated with the widget, or false if none
	 */

	public static function get_post_id_from_widget( $widget_type = '', $panel_widget ) {

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

			case 'AddDiscussionForumWidget':
				$post_id_key = 'discussion_forum_post_id';
				break;

			case 'AddLectureWidget':
				$post_id_key = 'lecture_post_id';
				break;

			case 'AddQuizWidget':
				$post_id_key = 'quiz_post_id';
				break;

			default:
				break;
		}

		if ( false === $post_id_key ) {
			return false;
		}

		return $panel_widget[ $post_id_key ];

	}/* get_post_id_from_widget() */


	/**
	 * Get the widget type from the widget class, allows us to know what to do
	 * or get from the other data
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $class - The class used to generate the widget
	 * @return (string|false) The type of widget, i.e. 'AddAssignmentWidget' or 'AddLinkWidget' or false if it's not a custom UBC Press widget
	 */

	public static function get_panels_widget_type( $class = '' ) {

		// List of ubc-press widgets
		$ubc_press_widgets = \UBC\Press\Plugins\SiteBuilder\Widgets\Setup::$registered_ubc_press_widgets;

		foreach ( $ubc_press_widgets as $id => $widget_class ) {
			if ( strpos( $class, $widget_class ) ) {
				return $widget_class;
			}
		}

		// It isn't one of ours, so let's get the answer from the default widgets
		$default_widget_class = \UBC\Press\Utils::get_default_panels_widget_type( $class );

		if ( $default_widget_class ) {
			return $default_widget_class;
		}

		return false;

	}/* get_panels_widget_type() */


	/**
	 * From the default set of widgets, return the type
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $class - The class used to generate the widget
	 * @return (string|false) The type of widget
	 */

	public static function get_default_panels_widget_type( $class ) {

		return $class;

	}/* get_default_panels_widget_type() */


	/**
	 * Add user notes for a specific section
	 *
	 * Usage: \UBC\Press\Utils::add_user_notes_for_object( $user_id, $post_id, $notes_content );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $user_id - The ID of the user for which we're adding the notes
	 * @param (int) $object_id - The object ID, probably Post ID of a section
	 * @param (int) $notes_content - The content of the note to add
	 * @return (bool) The return of add_user_meta
	 */

	public static function add_user_notes_for_object( $user_id = false, $object_id = false, $notes_content = '' ) {

		// Not signed in? No dice
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// No user passed? Use the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// No object passed? Assume in loop
		if ( false === $object_id ) {
			$object_id = get_the_ID();
		}

		// Bail early if we have neither
		if ( empty( $user_id ) || empty( $object_id ) ) {
			return false;
		}

		$user_id	= absint( $user_id );
		$object_id	= absint( $object_id );
		$site_id 	= absint( get_current_blog_id() );

		// Check that the current user is able to update this user's notes
		// Either a network admin, or the user themselves
		if ( ! is_super_admin() && ( get_current_user_id() !== $user_id ) ) {
			return false;
		}

		$notes_content = wp_kses_post( $notes_content );

		$new_note = array( 'content' => $notes_content, 'when' => time() );

		// Current notes for this user
		$current_notes = \UBC\Press\Utils::get_user_notes( $user_id );

		// Current user's notes total and... for this site
		if ( ! isset( $current_notes ) || ! is_array( $current_notes ) ) {
			$current_notes = array();
		}

		if ( ! isset( $current_notes[ $site_id ] ) || ! is_array( $current_notes[ $site_id ] ) ) {
			$current_notes[ $site_id ] = array();
		}

		// Add this one (replacing any existing one for this object)
		$current_notes[ $site_id ][ $object_id ] = $new_note;

		// Save
		return update_user_meta( $user_id, 'ubc_press_user_notes', $current_notes );

	}/* add_user_notes_for_section() */


	/**
	 * A getter for all user notes
	 *
	 * Usage: \UBC\Press\Utils::get_user_notes( $user_id );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $user_id - The ID of the user for whom we are getting notes
	 * @return (array) An array of user notes, based on site and section
	 */

	public static function get_user_notes( $user_id = false ) {

		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );

		return get_user_meta( $user_id, 'ubc_press_user_notes', true );

	}/* get_user_notes() */


	/**
	 * Get all notes for a specific user for a specific site. Default to
	 * current user on current site.
	 *
	 * Usage: \UBC\Press\Utils::get_user_notes_for_site()
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The ID of the user for whom we wish to get the notes
	 * @param int $site_id The ID of the site for which we want the notes for $user_id
	 * @return null
	 */

	public static function get_user_notes_for_site( $user_id = false, $site_id = false ) {

		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );

		if ( false === $site_id ) {
			$site_id = get_current_blog_id();
		}

		$site_id = absint( $site_id );

		if ( ! $user_id || ! $site_id ) {
			return array();
		}

		$all_notes = \UBC\Press\Utils::get_user_notes( $user_id );

		if ( ! isset( $all_notes ) || ! isset( $all_notes[ $site_id ] ) ) {
			return array();
		}

		return $all_notes[ $site_id ];

	}


	/**
	 * Check if a Gravity Form exists with the passed title.
	 *
	 * Usage: \UBC\Press\Utils::gform_exists( $title );
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $title - The title of a form to check
	 * @return (bool) true if a form exists with this title, false otherwise
	 */

	public static function gform_exists( $title ) {

		if ( ! class_exists( '\RGFormsModel' ) ) {
			return false;
		}

		$form_exists = false;

		$forms = \RGFormsModel::get_forms( null, 'title' );

		if ( count( $forms ) > 0 ) {
			foreach ( $forms as $form ) {
				if ( $form->title === $title ) {
					$form_exists = true;
				}
			}
		}

		return $form_exists;

	}/* gform_exists() */


	/**
	 * Generate a random string of given length. [A-Z0-9]
	 * From http://stackoverflow.com/a/5444902/308455
	 *
	 * Usage: \UBC\Press\Utils::random_string_of_length( 10 );
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $length - The number of characters of the string
	 * @return (string) A random string of given length
	 */

	public static function random_string_of_length( $length = 10 ) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $string;

	}/* random_string_of_length() */


	public static function component_is_saved_for_later( $post_id, $user_id ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// i.e. array( '1' => array( 23 => array( 'when' => 345676543 ), 54 => array( 'when' => 34567123 ) ) )
		$saved = \UBC\Press\Utils::get_saved_components_for_user( $user_id );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		if ( ! array_key_exists( $site_id, $saved ) ) {
			return false;
		}

		if ( array_key_exists( $post_id, $saved[ $site_id ] ) ) {
			return true;
		} else {
			return false;
		}

	}/* component_is_saved_for_later() */


	public static function get_saved_components_for_user( $user_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$user_id = absint( $user_id );

		// Current meta saved for this component
		$saved_for_later = get_user_meta( $user_id, 'ubc_press_saved_for_later', true );

		return $saved_for_later;

	}/* get_saved_components_for_user() */


	public static function get_saved_components_for_user_for_site( $user_id = false, $site_id = false ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$user_id = absint( $user_id );

		// If no site ID is passed, default to the current site
		if ( false === $site_id ) {
			$site_id = get_current_blog_id();
		}

		// Sanitize
		$site_id = absint( $site_id );

		if ( ! $user_id || ! $site_id ) {
			return array();
		}

		$all_saved = \UBC\Press\Utils::get_saved_components_for_user( $user_id );

		if ( ! isset( $all_saved[ $site_id ] ) ) {
			return array();
		}

		return $all_saved[ $site_id ];

	}


	public static function get_post_parent_title( $post_id = false ) {

		$post = static::get_post_from_post_id_or_false( $post_id );

		return get_the_title( $post->post_parent );

	}

	public static function get_post_parent_permalink( $post_id = false ) {

		$post = static::get_post_from_post_id_or_false( $post_id );

		return get_permalink( $post->post_parent );

	}

	public static function get_post_from_post_id_or_false( $post_id = false ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return false;
		}

		$post_args = array( 'post_type' => 'any', 'p' => $post_id );
		$query = new \WP_Query( $post_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		$this_post = $query->post;
		if ( ! is_a( $this_post, 'WP_Post' ) ) {
			return false;
		}

		return $this_post;

	}


	public static function set_component_as_saved_for_later( $post_id, $user_id ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_saved = \UBC\Press\Utils::get_saved_components_for_user( $user_id );

		// Ensure it's an array
		if ( ! is_array( $current_saved ) ) {
			$current_saved = array();
		}

		if ( ! array_key_exists( $site_id, $current_saved ) ) {
			$current_saved[ $site_id ] = array();
		}

		// Add our completion
		global $post;
		$current_saved[ $site_id ][ $post_id ] = array( 'when' => time(), 'saved_from' => esc_url( $_SERVER['HTTP_REFERER'] ) );

		do_action( 'ubc_press_set_component_as_saved', $post_id, $user_id );

		return (bool) update_user_meta( $user_id, 'ubc_press_saved_for_later', $current_saved );

	}/* set_component_as_saved_for_later() */


	public static function set_component_as_not_saved_for_later( $post_id, $user_id ) {

		// Bail if user isn't logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_saved = \UBC\Press\Utils::get_saved_components_for_user( $user_id );

		// Ensure it's an array
		if ( ! is_array( $current_saved ) ) {
			$current_saved = array();
		}

		if ( ! array_key_exists( $site_id, $current_saved ) ) {
			$current_saved[ $site_id ] = array();
		}

		if ( array_key_exists( $post_id, $current_saved[ $site_id ] ) ) {
			unset( $current_saved[ $site_id ][ $post_id ] );
		}

		do_action( 'ubc_press_set_component_as_not_saved', $post_id, $user_id );

		return (bool) update_user_meta( $user_id, 'ubc_press_saved_for_later', $current_saved );

	}/* set_component_as_not_saved_for_later() */


	public static function get_groups_for_user( $user_id = false ) {

		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		return wp_get_terms_for_user( $user_id, 'user-group' );

	}

	public static function get_users_in_group( $group_id ) {
		return wp_get_users_of_group( array( 'term' => absint( $group_id ), 'term_by' => 'ID' ) );
	}

	/**
	 * Get just the top-level sections list
	 *
	 * Usage: \UBC\Press\Utils::get_section_list()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_section_list() {

		$args = array(
			'post_type' => 'section',
			'post_parent' => 0,
			'posts_per_page' => '-1',
			'fields' => 'ids',
			'orderby' => 'menu_order parent date',
			'order' => 'ASC',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$the_query = new \WP_Query( $args );

		if ( ! $the_query->have_posts() ) {
			return array();
		}

		$sections = $the_query->posts;

		wp_reset_postdata();

		return $sections;

	}/* get_section_list() */

	/**
	 * Get just the full sections list
	 *
	 * Usage: \UBC\Press\Utils::get_section_list()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_full_section_list() {

		$args = array(
			'post_type' => 'section',
			'posts_per_page' => '-1',
			'fields' => 'ids',
			'orderby' => 'menu_order parent date',
			'order' => 'ASC',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$the_query = new \WP_Query( $args );

		if ( ! $the_query->have_posts() ) {
			return array();
		}

		$sections = $the_query->posts;

		wp_reset_postdata();

		return $sections;

	}/* get_section_list() */

	/**
	 * Get section ids with specified comonent type
	 *
	 * Usage: \UBC\Press\Utils::get_section_list()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function ubc_press_get_section_ids_with_component_type( $id ) {

		$section_ids 	= $id;

		if ( is_array( $section_ids ) ) :

			return;

		endif;

		$section_component_id = get_post_meta( $section_ids, 'component_associations', true );

		$post_type = get_post_type( $section_component_id );

		return $post_type;

	}

	/**
	 * A function to get sections meta vaule component associations
	 *
	 * Usage: \UBC\Press\Utils::get_all_section_component_meta_value()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_all_section_component_meta_value( $post_id = '' ) {

		$section_component_ids = get_post_meta( $post_id, 'component_associations', true );
		$post_type 						 = array();
		$args = array();

		if ( ! empty( $section_component_ids ) ) :

			foreach ( $section_component_ids as $section_component_id ) :

				$post_type[] = get_post_type( $section_component_id );

			endforeach;

			$args['id'] = $post_id;
			$args['type'] = $post_type;

		endif;

			return $args;

	}

	/**
	 * A function to get componet types for a section's components
	 *
	 * Usage: \UBC\Press\Utils::get_component_types_from_section_id()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_component_types_from_section_id( $post_ids = '' ) {

		if ( ! $post_ids ) :

			return;

		endif;

		$sections_id_with_components_type = array();

		if ( is_array( $post_ids ) ) :

			foreach ( $post_ids as $post_id ) :

				$section_function = \UBC\Press\Utils::get_all_section_component_meta_value( $post_id );

				if ( ! empty( $section_function['type'] ) ) :

					$sections_id_with_components_type[] = $section_function;

			endif;

			endforeach;

				return $sections_id_with_components_type;

		else :

			$section_function 		= \UBC\Press\Utils::get_all_section_component_meta_value( $post_ids );

			$sections_id_with_components_type[] = $section_function;

			return $sections_id_with_components_type;

		endif;

	}

	/**
	 * Get section ids with specified comonent type
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_section_ids_with_component_type() {

		$section_ids 														= \UBC\Press\Utils::get_full_section_list();
		$get_component_types_from_section_id 		= \UBC\Press\Utils::get_component_types_from_section_id( $section_ids );

		return $get_component_types_from_section_id;

	}

	/**
	 * Get section ids with specified comonent type, ie. reading
	 *
	 * Usage: \UBC\Press\Utils::ubc_press_get_sections_by_component()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function ubc_press_get_sections_by_component( $post_type = '' ) {

		if ( empty( $post_type ) ) {

			return;

		}

		$ids = array();

		$components 	 = \UBC\Press\Utils::get_section_ids_with_component_type();

		foreach ( $components as $component ) {

			if ( ! empty( $component['type'] ) ) :

				if ( in_array( $post_type, $component['type'] ) ) :

					$ids[] = $component['id'];

				endif;

			endif;

		}

		if ( empty( $ids ) ) {

			return;
		}

		$ids = ! empty( $ids ) ? implode( ', ', $ids ) : '';

		$agrs = array(

			'title_li'  => '',
			'include'   => $ids,
			'post_type' => 'section',
		);

		$get_sections = get_pages( $agrs );

		return $get_sections;

	}

	/* determine if we are on the user dashboard
	*
	* Usage: \UBC\Press\Utils::get_section_list()
	*
	* @since 1.0.0
	*
	* @param null
	* @return null
	*/

	public static function ubc_press_is_user_dashboard_page() {

		$dashboard_page 							= get_query_var( 'studentdashboard' );

		if ( 'yes' === $dashboard_page ) {

			return true;

		}

	}

	/* determine if we are on the user dashboard
	*
	* Usage: \UBC\Press\Utils::get_section_list()
	*
	* @since 1.0.0
	*
	* @param null
	* @return null
	*/

	public static function ubc_press_is_section_component_page() {

		$get_coursecontent 	= get_query_var( 'get_coursecontent' );

		if ( 'yes' === $get_coursecontent ) {

			return true;

		}

	}

	/* determine if we are on the section with component type or dashboard
	*
	* Usage: \UBC\Press\Utils::ubc_press_special_pages()
	*
	* @since 1.0.0
	*
	* @param null
	* @return null
	*/

	public static function ubc_press_is_special_pages() {

		$dashboard_page 	 = \UBC\Press\Utils::ubc_press_is_user_dashboard_page();
		$get_coursecontent = \UBC\Press\Utils::ubc_press_is_section_component_page();

		if ( true !== $dashboard_page && true !== $get_coursecontent ) {

			return;

		}

		return true;

	}

	/* determine if we are on a section page
	*
	* Usage: \UBC\Press\Utils::ubc_press_is_section()
	*
	* @since 1.0.0
	*
	* @param null
	* @return null
	*/

	public static function ubc_press_is_section() {

		$get_post_type 		= is_singular( 'section' );

		if ( 'section' !== $get_post_type ) {

			return;

		}

		return true;

	}

}/* Utils */
