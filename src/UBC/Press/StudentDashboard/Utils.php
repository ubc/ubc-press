<?php

namespace UBC\Press\StudentDashboard;

/**
 * Utils for our student dashboard
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage StudentDashboard
 *
 */

class Utils {

	/**
	 * Get the total course completion level. i.e. if there are 10
	 * completable components in this course, and the current student
	 * has completed 7 of them, we report those details.
	 *
	 * @since 1.0.0
	 *
	 * @param null|int $user_id - The ID of a user, or null for current user
	 * @return array An array of course completion data
	 */

	public static function get_course_completion_data_for_student( $user_id = null ) {

		// Sanitize and default to current user ID
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );

		if ( ! $user_id ) {
			return array();
		}

		$pre_data = array();

		// First we'll need a list of sections and their completable components components.
		$sections = \UBC\Press\Utils::get_section_list();

		if ( empty( $sections ) ) {
			return array();
		}

		$num_of_components = 0;
		$num_completed_components = 0;

		foreach ( $sections as $key => $post_id ) {

			$kids = get_children( $post_id );
			if ( empty( $kids ) ) {
				continue;
			}

			foreach ( $kids as $id => $subsection_post ) {
				$subsection_data = \UBC\Press\Utils::completed_components_for_section( $subsection_post->ID );
				$pre_data[] = array(
					'id' => $subsection_post->ID,
					'data' => $subsection_data,
				);

				$num_of_components = (int) $num_of_components + (int) $subsection_data['total_components'];
				$num_completed_components = (int) $num_completed_components + (int) $subsection_data['completed_components'];
			}

		}

		$data = array(
			'data' => $pre_data,
			'num_components' => $num_of_components,
			'num_completed_components' => $num_completed_components,
		);

		return $data;

	}/* get_course_completion_data_for_student() */


	/**
	 * Get the total course completion percentage for a particular student
	 *
	 * @since 1.0.0
	 *
	 * @param null|int $user_id - The ID of a user, or null for current user
	 * @return string
	 */

	public static function get_total_course_completion_percentage_for_student( $user_id = null ) {

		// Sanitize and default to current user ID
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );

		if ( ! $user_id ) {
			return '0';
		}

		$data = \UBC\Press\StudentDashboard\Utils::get_course_completion_data_for_student( $user_id );

		if ( 0 === (int) $data['num_completed_components'] ) {
			return 0;
		}

		$number = (int) $data['num_completed_components'] / (int) $data['num_components'];

		return ( number_format( (float) $number, 2, '.', '' ) ) * 100;

	}/* get_total_course_completion_percentage_for_student() */

}/* Utils */
