<?php

namespace UBC\Press\Plugins;

/**
 * Setup for our custom meta boxes
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Metaboxes
 *
 */


class Setup {


	/**
	 * Initialize each inidivual plugin tie-up
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		$this->setup_sitebuilder();

		// $this->setup_poststoposts();

		// Members/WP User Groups
		$this->setup_members();

	}/* init() */


	/**
	 * SiteOrigin SiteBuilder plugin mods
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_sitebuilder() {

		// @TODO: If not active need to show message on Add New Section screen
		if ( ! defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			return;
		}

		$sitebuilder = new \UBC\Press\Plugins\SiteBuilder\Setup;
		$sitebuilder->init();

	}/* setup_sitebuilder() */



	/**
	 * Justin Tadlock's members plugin mods
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_members() {

		$members = new \UBC\Press\Plugins\Members\Setup;
		$members->init();

	}/* setup_members() */

}/* class Setup */
