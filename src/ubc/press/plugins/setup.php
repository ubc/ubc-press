<?php

namespace UBC\Press\Plugins;

/**
 * Setup for our custom meta boxes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Metaboxes
 *
 */


class Setup {


	/**
	 * Initialize each inidivual plugin tie-up
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		$this->setup_sitebuilder();

		// Members/WP User Groups
		$this->setup_members();

		// wp-event-calendar
		$this->setup_wp_event_calendar();

		// WP User Groups by JJJ
		$this->setup_wp_user_groups();

		// WP Pro Quiz
		$this->setup_wp_pro_quiz();

	}/* init() */


	/**
	 * SiteOrigin SiteBuilder plugin mods
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_sitebuilder() {

		// @TODO: If not active need to show message on Add New Section screen
		if ( ! defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			return;
		}

		$sitebuilder = new \UBC\Press\Plugins\SiteBuilder\Setup;
		$sitebuilder->init();

	}/* setup_sitebuilder() */



	/**
	 * Justin Tadlock's members plugin mods
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_members() {

		$members = new \UBC\Press\Plugins\Members\Setup;
		$members->init();

	}/* setup_members() */


	/**
	 * JJJ's wp-event-calendar plugin
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_wp_event_calendar() {

		if ( ! function_exists( 'wp_event_calendar' ) ) {
			return;
		}

		$wpeventcalendar = new \UBC\Press\Plugins\WPEventCalendar\Setup;
		$wpeventcalendar->init();

	}/* setup_wp_event_calendar() */


	/**
	 * JJJ's WP User Groups plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_wp_user_groups() {

		if ( ! function_exists( '_wp_user_groups' ) ) {
			return;
		}

		$wpusergroups = new \UBC\Press\Plugins\WPUserGroups\Setup;
		$wpusergroups->init();

	}/* setup_wp_user_groups() */


	/**
	 * WP Pro Quiz setup
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_wp_pro_quiz() {

		if ( ! function_exists( 'wpProQuiz_autoload' ) ) {
			return;
		}

		$wpproquiz = new \UBC\Press\Plugins\WPProQuiz\Setup;
		$wpproquiz->init();

	}/* setup_wp_pro_quiz() */

}/* class Setup */
