<?php

namespace UBC\Press\API;

/**
 * For all of the WP Rest API Additions
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage API
 *
 */

class Setup extends \UBC\Press\ActionsBeforeAndAfter {


	/**
	 * Initialize ourselves
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
	 * Set up our add_action() calls
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// Add component_associations meta field to requests
		add_action( 'rest_api_init', array( $this, 'rest_api_init__add_component_associations' ) );

		// Register a new route to get a component by ID regardless of post type
		add_action( 'rest_api_init', array( $this, 'rest_api_init__register_component_route' ) );

	}/* setup_actions() */


	/**
	 * Set up our add_filter() calls
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_actions() */


	/**
	 * Add the component_associations meta field to the return of sections in the
	 * REST API
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function rest_api_init__add_component_associations() {

		register_rest_field( 'section',
	        'component_associations',
	        array(
	            'get_callback'    => array( $this, 'get_component_associations' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	}/* rest_api_init__add_component_associations() */


	/**
	 * Get the component_associations meta field for the given object (section)
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $object Details of current post.
	 * @param (string) $field_name Name of field.
	 * @param (WP_REST_Request) $request Current request
	 *
	 * @return mixed
	 */

	public function get_component_associations( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}/* get_component_associations() */



	/**
	 * Register a /components/{id} route to fetch a component by its ID
	 *
	 * @since 1.0.0
	 *
	 * @param (WP_REST_Request Object) $request - The WP_REST_Request object
	 * @return null
	 */

	public function rest_api_init__register_component_route( $request ) {

		// Matches ids to 12,373,3,123 and 123
		// Will need to split by comma, watch out for 123, (will end with a comma)
		register_rest_route( 'ubc-press/v1', '/components/(?P<ids>([0-9]+[,]*)*)', array(
	        'methods' => 'GET',
	        'callback' => array( $this, 'register_rest_route__component_callback' ),
	        'args' => array(
	            'id' => array(
	                'validate_callback' => array( $this, 'register_rest_route__component_callback__component_validate_callback' ),
	            ),
	        ),
	    ) );

	}/* rest_api_init__register_component_route() */



	/**
	 * The register_rest_route callback for regiestering the /components/{id} route
	 * This looks at the ID specified and then fetches that from the database.
	 * As all components are custom post types, we first look at what type it is
	 * and then grab it
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function register_rest_route__component_callback( $request ) {

		// Fetch the desired component ID
		$ids = $request->get_param( 'ids' );

		// Do a bit of massaging with this as it might be multiple, or single or end with a comma
		$ids = $this->get_ids( $ids );

		if ( empty( $ids ) ) {
			return new \WP_Error( 'no_component_id', __( 'No component ID Passed', 'ubc-press' ) );
		}

		// $ids is now an array with 1 or more items
		$args = array(
			'ignore_sticky_posts'	=> true,
			'post__in'				=> $ids,
			'post_type'				=> 'any',
			'post_status'			=> 'publish',
			'posts_per_page'		=> count( $ids ),
		);

		$query = new \WP_Query( $args );

		$posts = $query->posts;

		if ( empty( $posts ) ) {
			return new \WP_Error( 'no_component_found_in_loop', __( 'Nothing found for this component ID', 'ubc-press' ) );
		}

		$response = new \WP_REST_Response( $posts );

		return $response;

	}/* register_rest_route__component_callback() */



	/**
	 * The route is quite generic. It can accept
	 * /components/1
	 * /components/1,2
	 * /components/1,2,3,
	 * We'll do a bit of massaging to ensure we match the right IDs
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $ids - The end of the request
	 * @return (array) - An array of IDs parsed from the request
	 */

	public function get_ids( $ids ) {

		$ids = explode( ',', $ids );

		return array_map( 'absint', $ids );

	}/* get_ids() */


	public function register_rest_route__component_callback__component_validate_callback( $value, $request, $parameter ) {
		return is_numeric( $value );
	}/* register_rest_route__component_callback__component_validate_callback() */

}/* Setup */
