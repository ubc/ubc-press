<?php

namespace UBC\Press\Plugins\WPUserGroups;

/**
 * Setup for our wp-user-groups plugin mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage WPUserGroups
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

		// We remove the default User Types taxonomy that the plugin adds
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme__remove_user_types' ) );

	}/* setup_actions() */

	/**
	 * Filters to modify items in WP User Groups
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_filters() */



	/**
	 * WP User Groups registers a User Types taxonomy. We don't want that.
	 * Fortunately, because JJJ is awesome, he makes it easy to deregister it.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function after_setup_theme__remove_user_types() {
		remove_action( 'init', 'wp_register_default_user_type_taxonomy' );
	}/* after_setup_theme__remove_user_types() */


}/* class Setup */
