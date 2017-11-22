<?php

namespace UBC\Press\SectionComponentPage;

/**
 * Setup for a page with section sorted by component type
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage SectionComponent
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
	 * Set up our add_action() calls for the SectionComponentPage
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// Add a /coursecontent/ rewrite rule (so no fake 'page' required)
		add_action( 'init', array( $this, 'init__add_section_components_page_rewrite_rule' ) );

	}/* setup_actions() */


	/**
	 * Set up our add_filter() calls for the SectionComponentPage
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

		// Add a /dashboard/ rewrite rule (so no fake 'page' required)
		add_filter( 'template_include', array( $this, 'template_include__add_section_components_rewrite_rule' ), 100 );

		add_filter( 'query_vars', array( $this, 'query_vars__add_section_components_rewrite_rule' ) );

	}/* setup_filters() */


	/**
	 * Add the /dashboard/ rewrite rule which will show the front-end student dashboard
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__add_section_components_page_rewrite_rule() {
		add_rewrite_rule( '^coursecontent[\/]?', 'index.php?get_coursecontent=yes&component=$matches[1]', 'top' );
		add_rewrite_tag( '%coursecontent%','([^&]+)' );
		add_rewrite_tag( '%component%','([^&]+)' );
				// flush_rewrite_rules();
	}/* init__add_section_components_page_rewrite_rule() */


	/**
	 * Add the dashboard query var to enable the /me/ rewrite
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $vars - Already existing query variables
	 * @return (array) Modified query variables with our added dashboard
	 */

	public function query_vars__add_section_components_rewrite_rule( $vars ) {

		$vars[] = __( 'get_coursecontent', 'ubc-press' );
		return $vars;

	}/* query_vars__add_section_components_rewrite_rule() */

	/**
	 * If someone hits a calendar URL, we show our calendar template
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function template_include__add_section_components_rewrite_rule( $original_template ) {

		$coursecontent_page = get_query_var( 'get_coursecontent' );

		if ( 'yes' !== $coursecontent_page ) {
			return $original_template;
		}

		return \UBC\Press::get_plugin_path() . 'src/UBC/Press/SectionComponentPage/templates/section-components.php';

	}/* template_include__add_dashboard_rewrite_rule() */


	// /**
	//  * CAS Plugin a ubc_cs_redirect parameter for us for wp-admin requests, but that
	//  * doesn't apply for wp-login.php URLs. So let's add that.
	//  *
	//  * @since 1.0.0
	//  *
	//  * @param string $login_url    The login URL.
	//  * @param string $redirect     The path to redirect to on login, if supplied.
	//  * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
	//  * @return $login_url
	//  */
  //
	// public function login_url__add_cs_redirect( $login_url, $redirect, $force_reauth ) {
  //
	// 	$login_url = add_query_arg( 'ubc_cs_redirect', urlencode( $redirect ), $login_url );
  //
	// 	return $login_url;
  //
	// }/* login_url__add_cs_redirect() */

}/* class Setup */
