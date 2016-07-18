<?php

namespace UBC\Press\Roles;

/**
 * A factory class for us to be able to create roles nice
 * and easily
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Roles
 *
 */

class Role {

	/**
	 * The actual role to be created
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $role
	 */

	static $role = '';


	/**
	 * The 'Display Name' for the role
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $display_name
	 */

	static $display_name = '';


	/**
	 * The Capabilities for this role
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $capabilities
	 */

	static $capabilities = array();


	/**
	 * The class constructor which allows us to create a new role.
	 *
	 * Usage: \UBC\Press\Roles\Role( 'slug', 'Display Name', array( 'caps' => 'things' ) )
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $role - The Role Name
	 * @param (string) display_name - The display name for the role
	 * @param (array) $capabilities - An array of capabilities for this role
	 * @return null
	 */

	public function __construct( $role = '', $display_name = '', $capabilities = array() ) {

		if ( empty( $role ) || empty( $display_name ) || ! is_array( $capabilities ) || empty( $capabilities ) ) {
			return new \WP_Error( 'incorrect_role_args', __( 'Incorrect arguments were passed to \UBC\Press\Roles\Role', \UBC\Press::get_text_domain() ) );
		}

		// Set our class properties so we have access to them easily
		static::$role 			= $role;
		static::$display_name 	= $display_name;
		static::$capabilities 	= $capabilities;

		// Run before we do anything so we can hook in and do...stuff
		$this->before();

		// Run the role name through a filter
		$this->filter_role_name();

		// Run the display name through a filter
		$this->filter_display_name();

		// Run the capabilities through a filter
		$this->filter_capabilities();

		// Create our CTP object
		$this->create();

		// Run afer we have created the CPT so we can hook in
		$this->after();

	}/* __construct() */


	/**
	 * A method run before we create the role. Runs an action so we can hook in should we wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function before() {

		do_action( 'ubc_press_before_create_role', static::$role, static::$display_name, static::$capabilities );

	}/* before() */


	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function filter_role_name() {

		$role = static::$role;

		$role = sanitize_title_with_dashes( strtolower( trim( $role ) ) );

		/**
		 * Filters UBC Press Role Name
		 *
		 * A generic filter for UBC Press Role Names
		 *
		 * @since 1.0.0
		 *
		 * @param string $role a filtered string of the role
		 */

		$role = apply_filters( 'ubc_press_role_role',
			/**
			 * Filters the role name specifically for this role
			 *
			 * @since 1.0.0
			 *
			 * @param string $role string of this role name
			 */
			apply_filters( 'ubc_press_role_role_' . $role, $role )
		);

		static::$role = $role;

	}/* filter_role_name() */


	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function filter_display_name() {

		$display_name = static::$display_name;

		$display_name = sanitize_text_field( $display_name );

		/**
		 * Filters UBC Press Role Display Names
		 *
		 * A generic filter for UBC Press Role Display Name
		 *
		 * @since 1.0.0
		 *
		 * @param string $display_name a filtered string of the role
		 */

		$display_name = apply_filters( 'ubc_press_role_display_name',
			/**
			 * Filters the display name specifically for this role
			 *
			 * @since 1.0.0
			 *
			 * @param string $display_name string of this role display name
			 */
			apply_filters( 'ubc_press_role_display_' . $display_name, $display_name )
		);

		static::$display_name = $display_name;

	}/* filter_display_name() */


	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function filter_capabilities() {

		$capabilities = static::$capabilities;
		$role = strtolower( trim( static::$role ) );

		/**
		 * Filters UBC Press Role capabilities
		 *
		 * A generic filter for UBC Press Role Capabilities
		 *
		 * @since 1.0.0
		 *
		 * @param array $capabilities a filtered array of the role capabilities
		 */

		$capabilities = apply_filters( 'ubc_press_role_capabilities',
			/**
			 * Filters the display name specifically for this role
			 *
			 * @since 1.0.0
			 *
			 * @param array $capabilities array of the capabilities for this role
			 */
			apply_filters( 'ubc_press_role_capabilities_' . $role, $capabilities )
		);

		static::$capabilities = $capabilities;

	}/* filter_capabilities() */


	/**
	 * The method which actually creates the roles for us. Basically calls add_role()
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function create() {

		// In order to ensure that the capabilities are changed for a role should that
		// role already exist, we call remove_role() first
		remove_role( static::$role );

		// Now we add the role
		add_role( static::$role, static::$display_name, static::$capabilities );

	}/* create() */


	/**
	 * A method run after the role is created. Runs an action so we can hook in should we wish to.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function after() {

		do_action( 'ubc_press_after_create_role', static::$role, static::$display_name, static::$capabilities );

	}/* after() */



}/* class Role */
