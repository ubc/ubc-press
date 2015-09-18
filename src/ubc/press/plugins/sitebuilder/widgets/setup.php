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
	 * An array of registered UBC Press widgets
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var array
	 */

	public static $registered_ubc_press_widgets = array();

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


	public function check_dependencies() {

		if ( ! class_exists( 'SiteOrigin_Widget' ) ) {
			return false;
		}

		return true;

	}/* check_dependencies() */


	/**
	 * Register the Add Assignment Widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_assignment_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddAssignment\AddAssignmentWidget;

		static::$registered_ubc_press_widgets[] = 'AddAssignmentWidget';

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

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddHandout\AddHandoutWidget;

		static::$registered_ubc_press_widgets[] = 'AddHandoutWidget';

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

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddReading\AddReadingWidget;

		static::$registered_ubc_press_widgets[] = 'AddReadingWidget';

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

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddLink\AddLinkWidget;

		static::$registered_ubc_press_widgets[] = 'AddLinkWidget';

	}/* add_link_widget() */

}/* class Setup */
