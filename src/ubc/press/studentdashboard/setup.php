<?php

namespace UBC\Press\StudentDashboard;

/**
 * Setup for our front-end Student Dashboard
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage StudentDashboard
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

		$this->before();

		$this->setup_hooks();

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

		// Add a /dashboard/ rewrite rule (so no fake 'page' required)
		add_action( 'init', array( $this, 'init__add_dashboard_rewrite_rule' ) );

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

		// Add a /dashboard/ rewrite rule (so no fake 'page' required)
		add_filter( 'template_include', array( $this, 'template_include__add_dashboard_rewrite_rule' ) );

		add_filter( 'query_vars', array( $this, 'query_vars__add_dashboard_rewrite_rule' ) );

	}/* setup_filters() */


	/**
	 * Add the /dashboard/ rewrite rule which will show the front-end student dashboard
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__add_dashboard_rewrite_rule() {

		// /dashboard
		add_rewrite_rule( 'me[\/]?', 'index.php?studentdashboard=yes', 'top' );

	}/* init__add_dashboard_rewrite_rule() */


	/**
	 * Add the dashboard query var to enable the /me/ rewrite
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $vars - Already existing query variables
	 * @return (array) Modified query variables with our added dashboard
	 */

	public function query_vars__add_dashboard_rewrite_rule( $vars ) {

		$vars[] = __( 'studentdashboard', \UBC\Press::get_text_domain() );
		return $vars;

	}/* query_vars__add_dashboard_rewrite_rule() */

	/**
	 * If someone hits a calendar URL, we show our calendar template
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function template_include__add_dashboard_rewrite_rule( $original_template ) {

		$dashboard_page = get_query_var( 'studentdashboard' );

		if ( 'yes' !== $dashboard_page ) {
			return $original_template;
		}

		return \UBC\Press::get_plugin_path() . '/src/ubc/press/studentdashboard/templates/dashboard.php';

	}/* template_include__add_dashboard_rewrite_rule() */

}/* class Setup */
