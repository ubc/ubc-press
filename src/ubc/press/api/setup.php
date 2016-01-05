<?php

namespace UBC\Press\API;

/**
 * For all of the WP Rest API Additions
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage API
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
	 * Set up our add_action() calls
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

	}/* setup_actions() */


	/**
	 * Set up our add_filter() calls
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_actions() */


	/**
	 * Run before we make any api changes. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_setup_api' );

	}/* before() */


	/**
	 * Run an action after we make api changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_setup_api' );

	}/* after() */

}/* Setup */
