<?php

namespace UBC\Press\Help;

/**
 * For all of the different Help text we have across different screens
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Help
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

		// index.php is the dashboard
		add_action( 'load-index.php', array( $this, 'load__dashboard_help_tabs' ), 20 );

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

		// Remove default help tabs
		add_filter( 'contextual_help', array( $this, 'contextual_help__remove_defaults' ), 10, 3 );

	}/* setup_actions() */



	/**
	 * Remove default WordPress help tabs as we want to provide focussed help about
	 * the teaching and learning contexts we provide.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function contextual_help__remove_defaults( $old_help, $screen_id, $screen ) {

		$to_remove = array();

		switch ( $screen_id ) {

			case 'dashboard':
				$to_remove = array( 'overview', 'help-navigation', 'help-layout', 'help-content' );
			break;

			default:
			break;

		}

		if ( empty( $to_remove ) ) {
			return $old_help;
		}

		foreach ( $to_remove as $id => $help_id ) {
			$screen->remove_help_tab( $help_id );
		}

	}/* contextual_help__remove_defaults() */


	/**
	 * Load the help tabs output on the dashboard
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function load__dashboard_help_tabs() {

		$tabs = array(
			'PRESSDASHBOARDHELP' => array(
				'title'   => 'UBC Press',
				'content' => __( '
					<h3>About</h3>
					<p>UBC Press is a focused teaching and learning platform powered by WordPress. It is designed to provide a consistent experience for students on the front-end and a straightforward user interface for instructors and TAs.</p>
					<h3>Course Content</h3>
					<p>Your course is made up of content <em>sections</em>. Each section comprises of <em>components</em>. Components are items such as Handouts, Readings or Course Notes.</p>
					' , \UBC\Press::get_text_domain()
				),
			),
		);

		foreach ( $tabs as $id => $data ) {

			get_current_screen()->add_help_tab( array(
				 'id'       => $id,
				 'title'    => __( $data['title'], \UBC\Press::get_text_domain() ),
				 'content'  => __( $data['content'], \UBC\Press::get_text_domain() ),
			) );
		}

	}/* load__dashboard_help_tabs() */


	/**
	 * Run before we make any help changes. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_setup_help' );

	}/* before() */


	/**
	 * Run an action after we make help changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_setup_help' );

	}/* after() */

}/* Setup */
