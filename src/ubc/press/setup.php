<?php

namespace UBC\Press;

class Setup {

	/**
	 * Run our setup routine. We instantiate our custom post types, taxonomies, roles etc.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Setup our custom post types
		$this->setup_cpts();

		// Setup our custom taxonomies
		$this->setup_cts();

	}/* init() */


	/**
	 * Set up our custom post types
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_cpts() {

		// Set up post types
		$post_types = new \UBC\Press\CPTs\Setup;
		$post_types->init();

	}/* cpts() */


	/**
	 * Setup our custom taxonomies
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_cts() {

		// Set up post types
		$taxonomies = new \UBC\Press\CTs\Setup;
		$taxonomies->init();

	}/* setup_cts */

}/* class setup */
