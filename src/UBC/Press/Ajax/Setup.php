<?php

namespace UBC\Press\Ajax;

/**
 * Setup for our custom AJAX handler
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage AJAX
 * @see https://github.com/EkAndreas/ajaxflow
 *
 */

class Setup {


	/**
	 * The tag for the query var and rewrite rule
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (string) $ubc_press_ajax_tag
	 */

	public static $ubc_press_ajax_tag = '';


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Set our custom ajax tag
		static::$ubc_press_ajax_tag = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_tag();

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

		// Custom ajax endpoints
		add_action( 'init', array( $this, 'init__add_custom_ajax_endpoints' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect__custom_ajax_query' ) );

	}/* setup_actions() */


	/**
	 * Filters
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

		add_filter( 'query_vars', array( $this, 'query_vars__add_ajax_action_query_var' ) );

	}/* setup_filters() */


	/**
	 * Add a custom query var which allows us to know when we're making a
	 * UBC Press AJAX request
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $query_vars - Predefined custom query vars
	 * @return (array) Modified query vars
	 */

	public function query_vars__add_ajax_action_query_var( $query_vars ) {

		$query_vars[] = static::$ubc_press_ajax_tag;
		return $query_vars;

	}/* query_vars__add_ajax_action_query_var() */


	/**
	 * Add a rewrite tag and rule for our custom AJAX endpoint
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__add_custom_ajax_endpoints() {

		add_rewrite_tag( '%' . static::$ubc_press_ajax_tag . '%', '([^&]+)' );
		add_rewrite_rule(
			static::$ubc_press_ajax_tag . '/(.+?)/?$',
			'index.php?' . static::$ubc_press_ajax_tag . '=$matches[1]',
			'top'
		);
		if ( isset( $_REQUEST['q'] ) && strpos( $_REQUEST['q'], static::$ubc_press_ajax_tag ) === 1 ) {
			$this->ajax( str_replace( '/' . static::$ubc_press_ajax_tag . '/', '', $_REQUEST['q'] ) );
		}

	}/* init__add_custom_ajax_endpoints() */


	/**
	 * Determine if we're performing a UBC PRess AJAX call
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function template_redirect__custom_ajax_query() {

		$action = get_query_var( static::$ubc_press_ajax_tag );

		if ( empty( $action ) ) {
			return;
		}

		$this->ajax( $action );
		exit;

	}/* template_redirect__custom_ajax_query() */



	/**
	 * Perform a custom AJAX action. By default, this will produce actions, just
	 * like WP ajax, as follows;
	 *
	 * For logged in users;
	 * 'ubcpressajax_shortinit_load' and 'ubcpressajax_{action_name}'
	 *
	 * For non-logged in users
	 * 'ubcpressajax_shortinit_load' and 'ubcpressajax_nopriv_{action_name}'
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $action - The 'name' of the action we wish to perform
	 * @return (null)
	 */

	public function ajax( $action ) {

		define( 'DOING_AJAX', true );

		if ( empty( $action ) ) {
			return;
		}

		ini_set( 'html_errors', 0 );

		if ( ! wp_verify_nonce( $_REQUEST['ubcajaxnonce'], $action ) ) {
			wp_die( esc_html( __( 'Security check didn\'t pass, please check ubcajaxnonce!', \UBC\Press::get_text_domain() ) ) );
		}

		$shortinit = apply_filters( static::$ubc_press_ajax_tag . '_shortinit', false, $action );
		if ( $shortinit || ( isset( $_REQUEST['shortinit'] ) && $_REQUEST['shortinit'] ) ) {
			define( 'SHORTINIT', true );
		}

		require_once( ABSPATH . '/wp-load.php' );

		header( 'Content-Type: text/html' );
		send_nosniff_header();
		header( 'Cache-Control: no-cache' );
		header( 'Pragma: no-cache' );

		do_action( static::$ubc_press_ajax_tag . '_shortinit_load', $_REQUEST );

		if ( is_user_logged_in() ) {
			do_action( static::$ubc_press_ajax_tag . '_' . $action, $_REQUEST );
		} else {
			do_action( static::$ubc_press_ajax_tag . '_nopriv_' . $action, $_REQUEST );
		}

		wp_die( esc_html( __( 'Your ' . static::$ubc_press_ajax_tag . ' call does not exists or exit is missing in action!', \UBC\Press::get_text_domain() ) ) );
		exit;

	}/* ajax() */

}/* class Setup */
