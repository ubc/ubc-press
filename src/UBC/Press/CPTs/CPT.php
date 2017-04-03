<?php

namespace UBC\Press\CPTs;

/**
 * A factory class for us to be able to create post types nice
 * and easily.
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage CPTs
 *
 */

class CPT {

	/**
	 * The passed label args
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $label_args
	 */

	static $label_args = array();

	/**
	 * The passed WordPress args
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $wp_args
	 */

	static $wp_args = array();

	/**
	 * The passed icon slug
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $icon
	 */

	static $icon = '';

	/**
	 * The resultant CPT object
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $cpt_object
	 */

	static $cpt_object = false;


	/**
	 * To create a new custom post type we instantiate this class with
	 * at least a set up label arguments. We can optionally pass some
	 * WordPress args (just like for register_post_type() ) and also an
	 * icon
	 *
	 * Usage: $new_cpt = \UBC\Press\CPTs\CPT( array( 'post_type_name' => 'link','singular' => 'link', 'plural' => 'Links', 'slug' => 'link' ) );
	 *
	 * @since 1.0.0
	 *
	 * @see https://github.com/jjgrainger/wp-custom-post-type-class
	 * @param (array) $label_args - An array of label args.
	 * @param (array) $wp_args - A merged array of other CPT arguments
	 * @param (string) $icon - A dashicons slug for an icon (or an empty string for default) i.e. 'dashicons-admin-links'
	 * @return null
	 */

	public function __construct( $label_args, $wp_args = array(), $icon = '' ) {

		if ( ! $label_args || ! is_array( $label_args ) || ! array_key_exists( 'post_type_name', $label_args ) ) {
			return new \WP_Error( 'incorrect_cpt_args', __( 'Incorrect CPT args were passed to \UBC\Press\CPTs\CPT', 'ubc-press' ) );
		}

		// Set our class properties so we have access to them easily
		static::$label_args = $label_args;
		static::$wp_args 	= $wp_args;
		static::$icon 		= $icon;
		// Run before we do anything so we can hook in and do...stuff
		$this->before();

		// Run the label args through a filter
		$this->filter_label_args();

		// Run the WP args through a filter
		$this->filter_wp_args();

		// Create our CTP object
		$this->create();

		// Add an icon if we have one
		$this->add_icon();

		// Run afer we have created the CPT so we can hook in
		$this->after();

	}/* init() */



	/**
	 * Sets up filters for our custom post type label arguments
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function filter_label_args() {

		$cpt_label_args = static::$label_args;

		$cpt_name = strtolower( $cpt_label_args['post_type_name'] );

		/**
		 * Filters UBC Press Custom Post Type arguments
		 *
		 * A generic filter for UBC Press Custom Post Type arguments
		 *
		 * @since 1.0.0
		 *
		 * @param array $cpt_label_args a filtered associative array of post type args
		 */

		$cpt_label_args = apply_filters( 'ubc_press_post_type_args',
			/**
			 * Filters the post type args specifically for this post type
			 *
			 * An associative array of arguments for the link custom post type
			 *
			 * @since 1.0.0
			 *
			 * @param array $cpt_label_args array of post type args for the CPT class
			 */
			apply_filters( 'ubc_press_post_type_args_' . $cpt_name, $cpt_label_args )
		);

		static::$label_args = $cpt_label_args;

	}/* filter_label_args() */


	/**
	 * Sets up filters for our custom post type WordPress arguments
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function filter_wp_args() {

		$cpt_wp_args = static::$wp_args;

		$cpt_name = strtolower( static::$label_args['post_type_name'] );

		/**
		 * Filters UBC Press CPT arguments for the WP args (2nd parameter of the new CPT() call)
		 *
		 * For all the other arguments for the post type as provided by WordPress
		 *
		 * @since 1.0.0
		 *
		 * @param array array() An array of optional params that would be passed to WP to create the CPT
		 */

		$cpt_wp_args = apply_filters( 'ubc_press_wp_post_type_args',
			/**
			 * Filters the specific WP post type args for this post type
			 *
			 * @since 1.0.0
			 *
			 * @param array array() as per http://codex.wordpress.org/Function_Reference/register_post_type#Parameters
			 */
			apply_filters( 'ubc_press_wp_post_type_args_' . $cpt_name, $cpt_wp_args )
		);

		static::$wp_args = $cpt_wp_args;

	}/* filter_wp_args() */


	/**
	 * Method which actually sets up the CPT object by passing the filtered arguments
	 * to the main CPT class
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function create() {

		$post_type_object = new \CPT( static::$label_args, static::$wp_args );

		// Set the class property for this object
		static::$cpt_object = $post_type_object;

	}/* create() */



	/**
	 * Add an icon for the CPT if we have been asked for one
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function add_icon() {

		if ( empty( static::$icon ) ) {
			return;
		}

		if ( false === static::$cpt_object ) {
			return;
		}

		static::$cpt_object->menu_icon( static::$icon );

	}/* add_icon() */



	/**
	 * A method run before we actually do anything which runs an action so we hook hook in early
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_create_cpt', static::$label_args, static::$wp_args, static::$icon );

	}/* before() */



	/**
	 * A method run after we have created the CPT object, again, allowing us to hook in
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_create_cpt', static::$label_args, static::$wp_args, static::$icon, static::$cpt_object );

	}/* after() */


}/* class Setup */
