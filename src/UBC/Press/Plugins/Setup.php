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

		// Additional pieces for when we add a new 'course'
		$this->setup_sis_course_info_lookup();

		// bbPress
		$this->setup_bbpress();

		// Dequeue plugins' scripts and styles as we do it ourselves. @TODO This is lazy. Move into own method.
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_plugin_assets' ), 999 );

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

		if ( ! function_exists( '_wp_event_calendar' ) ) {
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


	/**
	 * UBC SIS Course Info Lookup setup
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_sis_course_info_lookup() {

		$courseinfolookup = new \UBC\Press\Plugins\SISCourseInfoLookup\Setup;
		$courseinfolookup->init();

	}/* setup_sis_course_info_lookup() */

	/**
	 * bbPress mods
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */
	public function setup_bbpress() {

		$bbpress = new \UBC\Press\Plugins\BBPress\Setup;
		$bbpress->init();

	}/* setup_bbpress() */

	/**
	 * Dequeue plugin assets from the mu-plugins we use. This allows us to include the styles ourselves in
	 * our build script to reduce the number of requests being made.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */
	public function dequeue_plugin_assets() {

		// Override this behaviour
		if ( true === apply_filters( 'ubc_press_do_not_dequeue_other_plugin_assets', false ) ) {
			return;
		}

		// Dy default we don't dequeue from the WP Dashboard
		if ( is_admin() && ( true === apply_filters( 'ubc_press_do_not_dequeue_other_plugin_assets_from_admin', true ) ) ) {
			return;
		}

		$styles_to_remove = array(
			/* bbPress */
			'bbp-default',
		);
		$scripts_to_remove = array(
			/* bbPress */
			'bbpress-editor',
			'bbpress-forum',
			'bbpress-topic',
			'bbpress-reply',
			'bbpress-user',
		);

		$styles_to_remove = apply_filters( 'ubc_press_dequeue_plugin_assets_styles', $styles_to_remove );
		$scripts_to_remove = apply_filters( 'ubc_press_dequeue_plugin_assets_scripts', $scripts_to_remove );

		// $this->deqeue_styles( $styles_to_remove );
		// $this->deqeue_scripts( $scripts_to_remove );

	}/* dequeue_plugin_assets() */


	/**
	 * Dequeue external plugin styles
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $assets - an array of keys of styles to dequeue
	 * @return null
	 */

	public function deqeue_styles( $assets = array() ) {

		if ( empty( $assets ) || ! is_array( $assets ) ) {
			return;
		}

		foreach ( $assets as $id => $asset_to_dequeue ) {
			wp_dequeue_style( $asset_to_dequeue );
		}

	}/* deqeue_styles() */

	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $assets - an array of keys of scripts to dequeue
	 * @return null
	 */

	public function deqeue_scripts( $assets = array() ) {

		if ( empty( $assets ) || ! is_array( $assets ) ) {
			return;
		}

		foreach ( $assets as $id => $asset_to_dequeue ) {
			wp_dequeue_script( $asset_to_dequeue );
		}

	}/* deqeue_scripts() */



}/* class Setup */
