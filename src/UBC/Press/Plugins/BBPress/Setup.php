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

	}/* setup_filters() */

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

		return get_permalink();

	}/* bbp_new_topicreply_redirect_to__do_not_redirect_to_bbpress_urls() */

}/* class UBC\Press\Plugins\BBPress\Setup */
