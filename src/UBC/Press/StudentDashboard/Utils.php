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

	/**
	 * Returns the dashboard url if logged in or it redirects to login
	 *
	 * @since 1.0.0
	 * @return string
	 */

	public static function ubc_press_get_dashboard_url() {

		$dashboard_url	= 'me';
		$get_dashboard_link = ( is_user_logged_in() ? site_url( esc_html( $dashboard_url ) ) : wp_login_url( get_permalink() ) );

			return $get_dashboard_link;

	}

	/**
	 * gets the dashboard url if logged in or it redirects to login with a tag and svg call
	 *
	 * @since 1.0.0
	 * @return string
	 */

	public static function ubc_press_dashboard_url() {

		$dashboard_url 				= \UBC\Press\StudentDashboard\Utils::ubc_press_get_dashboard_url();
		$dashboard_text 			= is_user_logged_in() ? 'User dashboard' : 'User login';
		$dashboard_svg 				= is_user_logged_in() ? 'dashboard' : 'user';

		$dashboard_link 	= '<a href="'. $dashboard_url .'" title="Go to '. esc_attr( $dashboard_text ) .'"><svg class="ui-icon" aria-hidden="true"><use xlink:href="#'. esc_attr( $dashboard_svg ) .'"></use></svg> '. esc_html( $dashboard_text ). '</a>';

			return $dashboard_link;

	}

	/**
	 * get array of tab name, icon, id
	 *
	 * @since 1.0.0
	 * @return array
	 */

	public static function ubc_press_tab_names() {

		// Checking to see if mark as completed is set to be used
		$show_mark_complete_btn = \UBC\Press\Utils::do_components_show_mark_as_complete();

		$tab_names = array(

			'notes', 'saved', 'groups', 'discussions',

		);

		if ( ! empty( $show_mark_complete_btn ) ) :

			array_unshift( $tab_names, 'progress' );

		endif;

			return $tab_names;

	}

	/**
	 * gets the dashboard url with text, one can also select a specific dashbaord tab
	 *
	 * @since 1.0.0
	 * @return string
	 */

	public static function ubc_press_go_to_dashboard_url( $tab_name = '', $tab_url_text = '', $target = '_blank' ) {

		$dashboard_url 	= \UBC\Press\StudentDashboard\Utils::ubc_press_get_dashboard_url();
		$usable_tabs 		= \UBC\Press\StudentDashboard\Utils::ubc_press_tab_names();
		$tabs_name			= ( ! empty( $tab_name ) ? $tab_name : 'dashboard' );
		$tab_url_text 	= ( ! empty( $tab_url_text ) ? $tab_url_text : 'Find all ' . $tabs_name );
		$url						= ( in_array( $tabs_name, $usable_tabs ) ? $dashboard_url .'/#dashboard-' . $tabs_name : $dashboard_url );

			return $dashboard_link 	= '<a href="'. esc_url( $url ) .'" title="Go to '. esc_attr( $tabs_name ) .'" target="' . esc_attr( $target ) . '">'. esc_html( $tab_url_text ). '</a>';

	}


}/* Utils */
