<?php

namespace UBC\Press\Theme;

/**
 * Setup for our theme changes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Theme
 *
 */


class Setup extends \UBC\Press\ActionsBeforeAndAfter {

	/**
	 * Our initializer
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
	 * Set up our add_action() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// Section archive page should reflect page order not published date
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts__section_archive_order' ) );

		// Load our custom AJAX js
		add_action( 'init', array( $this, 'init__load_ubc_press_ajax' ) );

		// Load custom CSS. @TODO: Place this into the main theme stylesheet
		add_action( 'init', array( $this, 'init__load_temp_stylesheet' ) );

		add_action( 'init', array( $this, 'ag_add_oembed_handlers' ) );

	}/* setup_actions() */

	function ag_add_oembed_handlers() {
		// https://admin.video.ubc.ca/index.php/kwidget/cache_st/1452816460/wid/_135/uiconf_id/11170395/entry_id/0_g5x5kgdf
		wp_oembed_add_provider( '#https://admin\.video\.ubc\.ca/index\.php/kwidget/cache_st/(\S*)/wid/_135/uiconf_id/11170395/entry_id/*#i', 'https://video.ubc.ca/oembed', true );
	}


	/**
	 * Set up our add_filter() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

		// Add enablejsapi=1 to youtube embeds and an ID
		add_filter( 'embed_oembed_html', array( $this, 'oembed_result__add_jsapi_to_youtube_url' ), 100, 4 );

		// Add attributes to a Vimeo video url
		add_filter( 'embed_oembed_html', array( $this, 'embed_oembed_html__add_api_to_vimeo_url' ), 100, 4 );

	}/* setup_actions() */



	/**
	 * Add some attributes to the youTube iframe. An ID and a enablejsapi=1
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $cache   The cached HTML result, stored in post meta.
	 * @param string $url     The attempted embed URL.
	 * @param array  $attr    An array of shortcode attributes.
	 * @param int    $post_ID Post ID.
	 * @return mixed $cache - modified cache
	 */

	public function oembed_result__add_jsapi_to_youtube_url( $cache, $url, $attr, $post_ID ) {

		// Search for these urls within $url
		$youtube_url_tests = array(
			'youtube.com',
			'youtu.be',
		);

		// Default to false
		$url_is_found = false;

		foreach ( $youtube_url_tests as $key => $search_url ) {
			if ( strpos( $url, $search_url ) !== false ) {
				$url_is_found = true;
				continue;
			}
		}

		if ( false === $url_is_found ) {
			return $cache;
		}

		$cache = str_replace( '?feature=oembed', '?feature=oembed&enablejsapi=1', $cache );
		$cache = str_replace( '<iframe', '<iframe id="youtube-embed-post-id-' . $post_ID . '"', $cache );
		return $cache;

	}/* oembed_result__add_jsapi_to_youtube_url() */


	/**
	 * Add api=1 and player_id={post_id} to vimeo URLs
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $cache   The cached HTML result, stored in post meta.
	 * @param string $url     The attempted embed URL.
	 * @param array  $attr    An array of shortcode attributes.
	 * @param int    $post_ID Post ID.
	 * @return mixed $cache - modified cache
	 */

	public function embed_oembed_html__add_api_to_vimeo_url( $cache, $url, $attr, $post_ID ) {

		// Search for these urls within $url
		$vimeo_url_tests = array(
			'vimeo.com',
		);

		// Default to false
		$url_is_found = false;

		foreach ( $vimeo_url_tests as $key => $search_url ) {
			if ( strpos( $url, $search_url ) !== false ) {
				$url_is_found = true;
				continue;
			}
		}
		if ( false === $url_is_found ) {
			return $cache;
		}

		$original_url = $url;

		// Test if the URL already has query params
		$query = parse_url( $url, PHP_URL_QUERY );

		$to_append = 'api=1&player_id=' . $post_ID;

		$url .= ( $query ) ? '&' : '?';
		$url .= $to_append;

		// Always use ssl, so check for http: and replace with https:
		$url = str_replace( 'http://', 'https://', $url );
		$original_url_with_ssl = str_replace( 'http://', 'https://', $original_url );

		// Need to have player.vimeo.com in the URL,
		$url = str_replace( 'http://vimeo.com/', 'https://player.vimeo.com/video/', $url );
		$url = str_replace( 'https://vimeo.com/', 'https://player.vimeo.com/video/', $url );
		$original_url_with_ssl = str_replace( 'http://vimeo.com/', 'https://player.vimeo.com/video/', $original_url_with_ssl );
		$original_url_with_ssl = str_replace( 'https://vimeo.com/', 'https://player.vimeo.com/video/', $original_url_with_ssl );

		// Need to replace the original URL which may be SSL
		$cache = str_replace( $original_url_with_ssl, $url, $cache );
		$cache = str_replace( $original_url, $url, $cache );

		// Add an ID to the iFrame
		$cache = str_replace( '<iframe', '<iframe id="vimeo-embed-post-id-' . $post_ID . '"', $cache );

		// And a class
		$cache = str_replace( '<iframe', '<iframe class="vimeo-embed"', $cache );

		return $cache;

	}/* embed_oembed_html__add_api_to_vimeo_url() */


	/**
	 * Adjust the section archive order so that it reflects the custom page order
	 * rather than the published date
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $query - the WP_Query object
	 * @return null
	 */

	public function pre_get_posts__section_archive_order( $query ) {

		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}

		if ( ! $query->is_post_type_archive( 'section' ) ) {
			return;
		}

		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );

	}/* pre_get_posts__section_archive_order() */


	/**
	 * Register, localize and enqueue our custom UBC Press AJAX
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__load_ubc_press_ajax() {

		// Front-end only
		if ( is_admin() ) {
			return;
		}

		wp_register_script( 'ubc_press_ajax', \UBC\Press::get_plugin_url() . 'src/UBC/Press/Theme/assets/js/ubc-press-ajax.js', array( 'jquery' ), null, true );

		$localized_data = array(
			'ajax_url'	=> \UBC\Press\Ajax\Utils::get_ubc_press_ajax_url(),
			'text'		=> array(
				'save' => __( 'Save', \UBC\Press::get_text_domain() ),
				'saved' => __( 'Saved', \UBC\Press::get_text_domain() ),
				'loading' => __( 'Loading', \UBC\Press::get_text_domain() ),
				'completed' => __( 'Completed', \UBC\Press::get_text_domain() ),
				'mark_as_complete' => __( 'Mark as complete', \UBC\Press::get_text_domain() ),
				'completed_just_now' => __( 'Completed just now', \UBC\Press::get_text_domain() ),
			),
		);

		wp_localize_script( 'ubc_press_ajax', 'ubc_press_ajax', $localized_data );

		wp_enqueue_script( 'ubc_press_ajax' );

	}/* init__load_ubc_press_ajax() */


	/**
	 * Temporary:
	 *
	 * Load the temp stylesheet
	 *
	 * @since 1.0.0
	 * @TODO Move this into the main stylesheet
	 *
	 * @param null
	 * @return null
	 */

	public function init__load_temp_stylesheet() {

		wp_enqueue_style( 'ubc-press-temp' );
		wp_register_style( 'ubc-press-temp-admin', \UBC\Press::get_plugin_url() . 'src/UBC/Press/Theme/assets/css/temp-admin.css' );
		wp_enqueue_style( 'ubc-press-temp-admin' );

		if ( is_admin() ) {
			wp_register_script( 'ubc-press-temp', \UBC\Press::get_plugin_url() . 'src/UBC/Press/Theme/assets/js/ubc-press-temp.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'ubc-press-temp' );
		}


	}/* init__load_temp_stylesheet() */

}/* class Setup */
