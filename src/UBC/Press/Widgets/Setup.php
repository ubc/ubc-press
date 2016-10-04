<?php

namespace UBC\Press\Widgets;

/**
 * For our widgets
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Help
 *
 */

class Setup extends \UBC\Press\ActionsBeforeAndAfter {


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Set up our hooks and filters
		$this->setup_hooks();
		// $this->setup_instructor_widget();

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

		add_action( 'widgets_init', array( $this, 'init_widgets' ) );

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

		// Add filters in here

	}/* setup_actions() */


	/**
	 * The default WordPress dashboard isn't great for a T&L Environment. Let's make a few changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function init_widgets() {

		$instructor_widget = new \UBC\Press\Widgets\InstructorWidget\Setup();
		register_widget( '\UBC\Press\Widgets\InstructorWidget\Setup' );
		// $instructor_widget->init();

		$handouts_widget = new \UBC\Press\Widgets\HandoutsWidget\Setup();
		register_widget( '\UBC\Press\Widgets\HandoutsWidget\Setup' );

	}/* setup_wp_dashboard() */


}/* Setup */
