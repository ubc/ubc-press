<?php

namespace UBC\Press\CPTs;

/**
 * Setup for our custom post types
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage CPTs
 *
 */


class Setup {


	/**
	 * An array of post types, and their arguments which we are going to
	 * set up
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $post_types_to_set_up
	 */

	static $post_types_to_set_up = array();



	/**
	 * Our initializer which determines and then creates our custom post types
	 * Also runs methods before and after creation which run actions enabling us
	 * to hook in if required
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Run an action so we can hook in beforehand
		$this->before();

		// Determine which CPTs to create
		$this->determine();

		// Create the CPTs
		$this->create();

		// Run an action so we can hook in afterwards
		$this->after();

	}/* init() */



	/**
	 * Determine which post types to set up. Initially this is just a forced array
	 * but it will allow us to have an option to turn on/off these
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function determine() {

		$post_types_to_set_up = array();

		$post_types_to_set_up['section'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'section',
				'singular' 			=> 'Section',
				'plural' 			=> 'Sections',
				'slug' 				=> 'section',
			),
			'wp_args' => array(
				'capability_type' => 'section',
				'map_meta_cap' => true,
				'supports' => array( 'title', 'page-attributes' ),
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'section',
				),
				'hierarchical' => true,
			),
			'icon' => 'dashicons-welcome-add-page',
		);

		$post_types_to_set_up['assignment'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'assignment',
				'singular' 			=> 'Assignment',
				'plural' 			=> 'Assignments',
				'slug' 				=> 'assignment',
			),
			'wp_args' => array(
				'capability_type' => 'assignment',
				'map_meta_cap' => true,
				'supports' => array( 'title' ),
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'assignment',
				),
			),
			'icon' => 'dashicons-media-text',
		);

		$post_types_to_set_up['handout'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'handout',
				'singular' 			=> 'Handout',
				'plural' 			=> 'Handouts',
				'slug' 				=> 'handout',
			),
			'wp_args' => array(
				'capability_type' => 'handout',
				'map_meta_cap' => true,
				'supports' => array( 'title' ),
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'handout',
				),
			),
			'icon' => 'dashicons-portfolio',
		);

		$post_types_to_set_up['note'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'note',
				'singular' 			=> 'Note',
				'plural' 			=> 'Notes',
				'slug' 				=> 'note',
			),
			'wp_args' => array(
				'capability_type' => 'note',
				'map_meta_cap' => true,
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'note',
				),
			),
			'icon' => 'dashicons-edit',
		);

		$post_types_to_set_up['reading'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'reading',
				'singular' 			=> 'Reading',
				'plural' 			=> 'Readings',
				'slug' 				=> 'reading',
			),
			'wp_args' => array(
				'capability_type' => 'reading',
				'map_meta_cap' => true,
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'reading',
				),
			),
			'icon' => 'dashicons-book-alt',
		);

		$post_types_to_set_up['link'] = array(
			'label_args' => array(
				'post_type_name' 	=> 'link',
				'singular' 			=> 'link',
				'plural' 			=> 'Links',
				'slug' 				=> 'link',
			),
			'wp_args' => array(
				'capability_type' => 'link',
				'map_meta_cap' => true,
				'rewrite' => array(
					'with_front' => false,
					'slug' => 'link',
				),
			),
			'icon' => 'dashicons-admin-links',
		);

		static::$post_types_to_set_up = $post_types_to_set_up;

	}/* determine() */



	/**
	 * Create the post types that have been determined
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function create() {

		// Fetch which post types are being set up
		$post_types_to_set_up = static::$post_types_to_set_up;

		// Bail early if we don't have any
		if ( empty( $post_types_to_set_up ) || ! is_array( $post_types_to_set_up ) ) {
			return;
		}

		// Loop over each one and create it
		foreach ( $post_types_to_set_up as $slug => $cpt_args ) {

			$label_args	= ( isset( $cpt_args['label_args'] ) ) ? $cpt_args['label_args'] : array();
			$wp_args 	= ( isset( $cpt_args['wp_args'] ) ) ? $cpt_args['wp_args'] : array();
			$icon 		= ( isset( $cpt_args['icon'] ) ) ? $cpt_args['icon'] : '';

			$post_type_object = new \UBC\Press\CPTs\CPT( $label_args, $wp_args, $icon );
		}

	}/* create() */


	/**
	 * Run before we create any custom post types. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_create_all_cpts' );

	}/* before() */



	/**
	 * Run an action after we create all post types.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_create_all_cpts' );

	}/* after() */


}/* class Setup */
