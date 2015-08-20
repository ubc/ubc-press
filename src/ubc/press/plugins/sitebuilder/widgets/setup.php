<?php

namespace UBC\Press\Plugins\SiteBuilder\Widgets;

/**
 * Setup for our custom widgets
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Widgets
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

		// Allow us to add an Assignment to a piece of content
		$this->add_assignment_widget();

		// Add a handout
		$this->add_handout_widget();

		// Add a reading
		$this->add_reading_widget();

		// Add a link
		$this->add_link_widget();

	}/* init() */


	/**
	 * Register the Add Assignment Widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_assignment_widget() {

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddAssignment\AddAssignmentWidget;

	}/* add_assignment_widget() */


	/**
	 * Register the Add HAndout Widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_handout_widget() {

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddHandout\AddHandoutWidget;

	}/* add_handout_widget() */



	/**
	 * Register the Add Reading widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_reading_widget() {

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddReading\AddReadingWidget;

	}/* add_reading_widget() */

	/**
	 * Register the Add Link widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */
	public function add_link_widget() {

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddLink\AddLinkWidget;

	}/* add_link_widget() */

}/* class Setup */
