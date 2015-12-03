<?php

namespace UBC\Press\Onboarding;

/**
 * The onboarding pieces for UBC Press.
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage OnBoarding
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
	 * Add our action hooks
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// On all admin pages we redirect to the dashboard if the user hasn't onboarded
		add_action( 'current_screen', array( $this, 'current_screen__redirect_to_dashboard_when_not_onboarded' ) );

		// On the dashboard, we show the onboarding if necessary
		add_action( 'admin_head-index.php', array( $this, 'admin_head_index__onboarding' ) );

		// Register the new dashboard widget with the 'wp_dashboard_setup' action
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup__add_dashboard_widget' ) );

	}/* setup_actions() */



	/**
	 * Register a dashboard widget. We need to do this in order to be able to use CMB2 on the
	 * dashboard. The widget just outputs the form, we then move it, via JS.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function wp_dashboard_setup__add_dashboard_widget() {
		// Test and bail early if we are not to show the onboarding
		if ( ! \UBC\Press\Onboarding\Utils::show_onboarding() ) {
			return;
		}
		wp_add_dashboard_widget( 'ubc_press_onboarding', __( 'Welcome to UBC Spaces', \UBC\Press::get_text_domain() ), array( $this, 'wp_add_dashboard_widget__widget_output' ) );
	}/* wp_dashboard_setup__add_dashboard_widget() */


	/**
	 * The output for the dashboard widget for the onboarding. Literally just the CMB2 form.
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return null
	 */

	public function wp_add_dashboard_widget__widget_output( $post, $callback_args ) {
		cmb2_metabox_form( 'ubc_press_onboarding_metabox', 'ubc_press_course_details' );
	}/* wp_add_dashboard_widget__widget_output() */


	/**
	 * Add our filters
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_filters() */


	/**
	 * On the dashboard, (to where people are redirected if they haven't onboarded)
	 * we show the onboarding procedure
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_head_index__onboarding() {

		// Test and bail early if we are not to show the onboarding
		if ( ! \UBC\Press\Onboarding\Utils::show_onboarding() ) {
			return;
		}

		$this->load_styles();
		$this->load_scripts();

	}/* admin_head_index__onboarding() */


	/**
	 * When an instructor or TA or NA first logs into the site, we onboard
	 * them. It's a bit like waterboarding, but with less water. And more
	 * on. Actually it's not anything like waterboarding. I digress.
	 * By default people are sent to the dashboard (and will therefore see
	 * the onboarding). However, if they are either sent to another admin page
	 * or try to be sneaky, we'll redirect them right back. This method handles
	 * that redirection.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function current_screen__redirect_to_dashboard_when_not_onboarded() {

		// If it's a super admin, bail
		if ( is_super_admin() ) {
			return;
		}

		// If this is not a TA/Instructor/Admin/Network Admin/Super Admin, bail
		// @TODO: Determine this level of access. (Custom perm? "can_onboard" ?)

		// If this is the main site in the network, don't do it
		if ( is_main_site_for_network( get_current_blog_id() ) ) {
			return;
		}

		// Test and bail early if we are not to show the onboarding
		if ( ! \UBC\Press\Onboarding\Utils::show_onboarding() ) {
			return;
		}

		// If we're on the dashboard, we don't redirect to the dashboard. That would be silly.
		$screen = get_current_screen();

		// Bail if for some reason we don't get a screen. We need a screen.
		if ( ! is_a( $screen, 'WP_Screen' ) ) {
			return;
		}

		// Bail if we're on the dashboard.
		if ( 'dashboard' === $screen->id ) {
			return;
		}

		// OK, we're not on the dashboard, we haven't been onboarded, back to the dashboard with you
		wp_redirect( admin_url() );
		exit;

	}/* current_screen__redirect_to_dashboard_when_not_onboarded() */


	/**
	 * Load the styles we need for our onboarding
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function load_styles() {

		wp_enqueue_style( 'ubc-press-onboarding', \UBC\Press::get_plugin_url() . 'src/ubc/press/onboarding/assets/css/ubc-press-onboarding.css' );

	}/* load_styles() */


	/**
	 * Load the javascript we need for our onboarding
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function load_scripts() {

		wp_register_script( 'ubc-press-onboarding', \UBC\Press::get_plugin_url() . 'src/ubc/press/onboarding/assets/js/ubc-press-onboarding.js' );

		$data = array(
			'template' => $this->fetch_template(),
		);

		wp_localize_script( 'ubc-press-onboarding', 'ubc_press_onboarding', $data );

		wp_enqueue_script( 'ubc-press-onboarding' );

	}/* load_scripts() */


	/**
	 * Load the template used to display the forms/details for our onboarding
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function fetch_template() {

		$template = \UBC\Helpers::fetch_template_part( \UBC\Press::get_plugin_path() . 'src/ubc/press/onboarding/templates/dashboard-welcome.php' );

		return wp_kses_post( $template );

	}/* fetch_template() */




	/**
	 * Run before we make any onboarding changes. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_setup_onboarding' );

	}/* before() */


	/**
	 * Run an action after we make onboarding changes
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_setup_onboarding' );

	}/* after() */

}/* class Setup */
