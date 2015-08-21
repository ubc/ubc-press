<?php

namespace UBC\Press\Theme;

/**
 * Setup for our theme changes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Theme
 *
 */


class Setup {

	/**
	 * Our initializer
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Run an action so we can hook in beforehand
		$this->before();

		// Set up our hooks and filters
		$this->setup_hooks();

		// Run an action so we can hook in afterwards
		$this->after();

	}/* init() */


	/**
	 * Setup hooks, actions and filters
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_hooks() {

		$this->setup_actions();

		$this->setup_filters();

	}/* setup_hooks() */


	/**
	 * Set up our add_action() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// Section archive page should reflect page order not published date
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts__section_archive_order' ) );

	}/* setup_actions() */


	/**
	 * Set up our add_filter() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_actions() */



	/**
	 * Adjust the section archive order so that it reflects the custom page oci_free_descriptor
	 * rather than the published date
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $query - the WP_Query object
	 * @return null
	 */

	public function pre_get_posts__section_archive_order( $query ) {

		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}

		if ( ! $query->is_post_type_archive( 'section' ) ) {
			return;
		}

		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );

	}/* pre_get_posts__section_archive_order() */


	/**
	 * Run before we make any theme changes. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_setup_theme' );

	}/* before() */


	/**
	 * Run an action after we make theme changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_setup_theme' );

	}/* after() */

}/* class Setup */
