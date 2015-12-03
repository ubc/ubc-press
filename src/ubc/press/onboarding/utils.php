<?php

namespace UBC\Press\Onboarding;

/**
 * Static utility methods used for onboarding
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Onboarding
 *
 */

class Utils {

	/**
	 * Determine if we should show the onboarding procedure. If we don't have the
	 * requisite details about this site (i.e. course ID, faculty, etc.) then we
	 * always show it. If we have the requisite details (say they've come from
	 * elsewhere) and the option 'ubc_press_onboarded' isn't set to a timestamp
	 * then we show it.
	 *
	 * Usage: \UBC\Press\Onboarding\Utils::show_onboarding();
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (bool) Whether we should show the onboarding or not. True if we should show it
	 */

	public static function show_onboarding() {

		// We need the WP Multi Network plugin to continue
		if ( ! function_exists( 'is_main_site_for_network' ) ) {
			return false;
		}

		// If we're on the main site dashboard, no.
		if ( is_main_site_for_network( get_current_blog_id() ) ) {
			return false;
		}

		// If we don't have these, we definitely must show it
		$course_details = static::get_course_details();

		if ( ! $course_details || empty( $course_details ) ) {
			return true;
		}

		// A unique identifier is made up of session-coursedept-courseno-sectno
		// So we need all of these things. Test
		$have_all_details = static::have_all_course_details( $course_details );

		if ( ! $have_all_details ) {
			return true;
		}

		// Now check for the ubc_press_onboarded option. If it's not set, show.
		$option_set = static::onboarded_option_set();

		if ( ! $option_set ) {
			return true;
		}

		// OK, we have all course details, the option has been set, no need for onboarding
		return apply_filters( 'ubc_press_show_onboarding', false, $course_details, $option_set );

	}/* show_onboarding() */


	/**
	 * Fetch the currently set course details. This is one option 'ubc_press_course_details'
	 * It's an associative array
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (array) The currently set course details
	 */

	public static function get_course_details() {

		$course_details = get_option( 'ubc_press_course_details', array() );

		return apply_filters( 'ubc_press_course_details', $course_details );

	}/* get_course_details() */


	/**
	 * Ensure that we have the required course details. We may collect other data
	 * but inititally we require session-coursedept-courseno-sectno
	 * The passed $course_details array is checked for array keys for each of
	 * those items. If any of them are empty, we return false. If all are set
	 * then we return true
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $course_details - The stored course details which we check for required fields
	 * @return (bool) true if we have all required course details, false otherwise
	 */

	public static function have_all_course_details( $course_details = array() ) {

		if ( ! $course_details || ! is_array( $course_details ) ) {
			return false;
		}

		$required_fields = array(
			'session',
			'course_dept',
			'course_num',
			'section_num',
		);

		$required_fields = apply_filters( 'ubc_press_required_course_details_fields', $required_fields, $course_details );

		// Sanity check, if there are no required fields, then, well, we have all the details
		if ( ! $required_fields || empty( $required_fields ) || ! is_array( $required_fields ) ) {
			return true;
		}

		// Now check $course_details for all of the keys in $required_fields
		foreach ( $required_fields as $id => $required_field ) {
			if ( ! array_key_exists( $required_field, $course_details ) ) {
				return false;
			}
		}

		// OK, we've looped over all of the require fields in the course details
		// and haven't bailed. So, we're good.
		return true;

	}/* have_all_course_details() */


	/**
	 * At the end of the onboarding procedure, we set an option ubc_press_onboarded
	 * to the timestamp when it was completed. Check for this option and that it is
	 * a timestamp. True if set and is a timestamp, false otherwise
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (bool) True if set and is a timestamp, false otherwise
	 */

	public static function onboarded_option_set() {

		$oboarded_option_value = get_option( 'ubc_press_onboarded', '' );

		if ( '' === $oboarded_option_value ) {
			return false;
		}

		if ( ! \UBC\Press\Utils::is_timestamp( $oboarded_option_value ) ) {
			return false;
		}

		// OK, we have the option, it's a timestamp, we consider the onboarding option set
		return true;

	}/* onboarded_option_set() */

}/* class Utils */
