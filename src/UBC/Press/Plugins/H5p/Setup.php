<?php

namespace UBC\Press\Plugins\H5p;

/**
 * H5p mods
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

		// add_action( 'wp_loaded', array( $this, 'ubc_press_h5p_get_content' ) );

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

	}/* setup_filters() */


	public static function ubc_press_h5p_get_content() {

		global $wpdb;

		if ( ! class_exists( 'H5P_Plugin_Admin' ) ) :

			return;

		endif;

		// Load input vars.

		$admin = \H5P_Plugin_Admin::get_instance();

		list($offset, $limit, $sort_by, $sort_dir, $filters, $facets) = $admin->get_data_view_input();

		$fields = array( 'title', 'content_type', 'id', 'content_type_id', 'slug' );

		// Add filters to data query
		$conditions = array();

		if ( isset( $filters[0] ) ) {

			$conditions[] = array( 'title', $filters[0], 'LIKE' );

		}

		if ( null !== $facets ) {

			$facetmap = array(
				'content_type' => 'content_type_id',
				'user_name' => 'user_id',
				'tags' => 'tags',
			);

			foreach ( $facets as $field => $value ) {

				if ( isset( $facetmap[ $fields[ $field ] ] ) ) {

					$conditions[] = array( $facetmap[ $fields[ $field ] ], $value, '=' );

				}
			}
		}

		// Create new content query
		$content_query = new \H5PContentQuery( $fields, $offset, $limit, $fields [ $sort_by ], $sort_dir, $conditions );

		$results = $content_query->get_rows();

		return $results;

	}/* ubc_press_h5p_get_content */

	public static function ubc_press_get_h5p_content_for_html_select() {

		$get_h5p_contents = \UBC\Press\Plugins\H5p\Setup::ubc_press_h5p_get_content();

		// Start fresh
		$return = array();

		if ( $get_h5p_contents ) :

			foreach ( $get_h5p_contents as $get_h5p_content ) :

				$title 					= $get_h5p_content->title;
				$h5p_content_id = $get_h5p_content->id;

				$return[ $h5p_content_id ] = $title;

			endforeach;

		endif;

		if ( empty( $return ) ) :

			return;

		endif;

		return $return;

	}/* ubc_press_get_h5p_content_for_html_select() */


}/* class UBC\Press\Plugins\BBPress\Setup */
