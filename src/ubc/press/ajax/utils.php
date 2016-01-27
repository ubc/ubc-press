<?php

namespace UBC\Press\Ajax;

/**
 * Utils for our custom AJAX
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage AJAX
 *
 */

class Utils {

	/**
	 * Get the ajax tag. It's used for the URLs for our custom AJAX requests
	 * Usage: \UBC\Press\Ajax\Utils::get_ubc_press_ajax_tag()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) the name/tag of the custom AJAX endpoint
	 */

	public static function get_ubc_press_ajax_tag() {

		return esc_html( apply_filters( 'ubc_press_ajax_tag', 'ubcpressajax' ) );

	}/* get_ubc_press_ajax_tag() */



	/**
	 * A helper method to create URLs for our custom AJAX requests.
	 * Basically site.com/{ajaxendpoint}/{action}
	 *
	 * Usage: \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( $action, $with_nonce, $nonce, $data )
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $action - The name of the AJAX action
	 * @param (bool) $with_nonce - Whether to include a _wpnonce param in the URL
	 * @param (string) $nonce - The WP nonce for this action
	 * @param (array) $data - Any other query args to add to the url
	 * @return (string) The URL for the AJAX request
	 */

	public static function get_ubc_press_ajax_action_url( $action, $with_nonce = false, $nonce = false, $data = false ) {

		$action = sanitize_text_field( $action );

		if ( false !== $with_nonce ) {
			$nonce = sanitize_text_field( $nonce );

			if ( false === $nonce || empty( $nonce ) ) {
				$nonce = wp_create_nonce( $action );
			}
		}

		$raw_url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_url();

		$url = $raw_url . trailingslashit( $action );

		if ( false !== $with_nonce ) {
			$url = add_query_arg( '_wpnonce', $nonce, $url );
		}

		if ( false !== $data && is_array( $data ) ) {
			$url = add_query_arg( $data, $url );
		}

		// Tack on a redirect URL which is where we came from
		$redirect_url = ( isset( $_SERVER['REQUEST_URI'] ) ) ? esc_url( $_SERVER['REQUEST_URI'] ) : false;

		if ( false !== $redirect_url ) {
			$url = add_query_arg( 'redirect_to', home_url( $redirect_url ), $url );
		}

		return esc_url( apply_filters( 'ubc_press_ajax_action_url', $url, $action, $with_nonce, $nonce ) );

	}/* get_ubc_press_ajax_action_url() */



	/**
	 * Helper method to just return the custom AJAX url without an action
	 *
	 * Usage: \UBC\Press\Ajax\Utils::get_ubc_press_ajax_url()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) {site_url}/{custom_ajax_endpoing_tag}
	 */

	public static function get_ubc_press_ajax_url() {

		$raw_url = trailingslashit( site_url() ) . trailingslashit( \UBC\Press\Ajax\Utils::get_ubc_press_ajax_tag() );

		return $raw_url;

	}/* get_ubc_press_ajax_url() */

}/* class Utils */
