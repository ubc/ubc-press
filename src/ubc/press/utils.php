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


}/* Utils */
