<?php

namespace UBC\Press\Plugins\BBPress;

/**
 * bbPress mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage bbPress
 *
 */

class Setup {

	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */

	public function init() {

		$this->setup_actions();

		$this->setup_filters();

	}/* init() */

	/**
	 * Add our action hooks
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */

	public function setup_actions() {

	}/* setup_actions() */


	/**
	 * Filters to modify items in bbPress
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */

	public function setup_filters() {

		add_filter( 'bbp_new_topic_redirect_to', array( $this, 'bbp_new_topicreply_redirect_to__do_not_redirect_to_bbpress_urls' ), 99, 3 );
		add_filter( 'bbp_new_reply_redirect_to', array( $this, 'bbp_new_topicreply_redirect_to__do_not_redirect_to_bbpress_urls' ), 99, 3 );

		add_filter( 'bbp_topic_admin_links', array( $this, 'bbp_topic_admin_links__edit_admin_links' ), 20, 2 );
		add_filter( 'bbp_reply_admin_links', array( $this, 'bbp_reply_admin_links__edit_admin_links' ), 20, 2 );		

	}/* setup_filters() */


	public function bbp_topic_admin_links__edit_admin_links( $links, $id ) {
	
		unset( $links['edit'] );
		unset( $links['merge'] );
		unset( $links['close'] );
		unset( $links['stick'] );
		unset( $links['trash'] );
		unset( $links['spam'] );
		unset( $links['approve'] );

		return $links;

	}

	public function bbp_reply_admin_links__edit_admin_links( $links, $id ) {

		unset( $links['edit'] );
		unset( $links['move'] );
		unset( $links['split'] );
		unset( $links['trash'] );
		unset( $links['spam'] );
		unset( $links['approve'] );
		unset( $links['unapprove'] );

		return $links;

	}


	/**
	 * When a new topic or reply is made from the forum or topic components, bbPress
	 * by default can redirect them to the custom post type URL. We don't want that.
	 * We want them to remain on the sub-section that they're on.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return string the URL of the sub-component we're on
	 */
	public function bbp_new_topicreply_redirect_to__do_not_redirect_to_bbpress_urls( $redirect_url, $redirect_to, $post_id ) {

		if ( is_admin() ) {
			return $redirect_url;
		}

		// If the forum is empty, then the redirect we want falls apart.
		// If it is, then we need to use the current URL
		return esc_url ( home_url( $_POST['_wp_http_referer'] ) );

	}/* bbp_new_topicreply_redirect_to__do_not_redirect_to_bbpress_urls() */

}/* class UBC\Press\Plugins\BBPress\Setup */
