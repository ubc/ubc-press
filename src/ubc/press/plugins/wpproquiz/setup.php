<?php

namespace UBC\Press\Plugins\WPProQuiz;

/**
 * Setup for our WP Pro Quiz Mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage WPProQuiz
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

		$this->setup_actions();

		$this->setup_filters();

	}/* init() */

	/**
	 * Add our action hooks
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

	}/* setup_actions() */



	/**
	 * Filters to modify items in WP Event Calendar
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_filters() */

}/* class Setup */
