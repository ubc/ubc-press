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

	public $user = null;



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
		 	'arts' => 'Arts',
			'medicine' => 'Medicine',
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
			'arts' => array( 'Philosophy', 'Drama' ),
			'medicine' => array( 'Nursing', 'Midwifery' ),
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

		// Now we need to know what program the current blog is attached to. That's stored
		// in the onboarding options ubc_press_course_details::program
		$course_details	= get_option( 'ubc_press_course_details' );
		$program 		= ( isset( $course_details['program'] ) ) ? $course_details['program'] : false;

		if ( false === $program ) {
			return array();
		}

		// Now we have that, we can look at the terms assoc
		$program_objectives = static::get_program_objectives_for_post_of_site( $program, $main_site_id );

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

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// i.e. array( '1' => array( 23 => array( 'when' => 345676543 ), 54 => array( 'when' => 34567123 ) ) )
		$completed = get_user_meta( $user_id, 'ubc_press_completed', true );

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

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = get_user_meta( $user_id, 'ubc_press_completed', true );

		// Ensure it's an array
		if ( ! is_array( $current_completed ) ) {
			$current_completed = array();
		}

		if ( ! array_key_exists( $site_id, $current_completed ) ) {
			$current_completed[ $site_id ] = array();
		}

		// Add our completion
		$current_completed[ $site_id ][ $post_id ] = array( 'when' => time() );

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

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = get_user_meta( $user_id, 'ubc_press_completed', true );

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

		// If no user ID is passed, default to the current user
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		// Sanitize
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$site_id = get_current_blog_id();

		// Current meta saved for this component
		$current_completed = get_user_meta( $user_id, 'ubc_press_completed', true );

		if ( ! isset( $current_completed[ $site_id ] ) || ! isset( $current_completed[ $site_id ][ $post_id ] ) ) {
			return '';
		}

		return $current_completed[ $site_id ][ $post_id ]['when'];

	}/* get_when_component_was_completed() */

}/* Utils */
