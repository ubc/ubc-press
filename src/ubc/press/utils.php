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

}/* Utils */
