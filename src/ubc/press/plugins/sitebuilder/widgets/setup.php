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

		$this->add_assignment_widget();

	}/* init() */


	public function add_assignment_widget() {

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddAssignment\AddAssignmentWidget;

	}/* add_assignment_widget() */

}/* class Setup */
