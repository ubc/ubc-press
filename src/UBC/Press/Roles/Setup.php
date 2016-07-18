<?php

namespace UBC\Press\Roles;

/**
 * Setup for our custom roles. This should only be done on plugin activation.
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Roles
 *
 */


class Setup extends \UBC\Press\ActionsBeforeAndAfter {

	/**
	 * Once determined, the roles get stored in this class property so they're accessible
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var (array) $roles
	 */

	static $roles = array();


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

		// Determine which roles to create
		$this->determine();

		// Create the roles
		$this->create();

		// Run an action so we can hook in afterwards
		$this->after();

	}/* init() */

	/**
	 * Determine which roles to create
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function determine() {

		$roles = array(

			'student' => array(
				'role' 			=> 'student',
				'display_name' 	=> __( 'Student', \UBC\Press::get_text_domain() ),
				'capabilities' 	=> array(
					'read' => true,
				),
			),

			'ta' => array(
				'role' 			=> 'ta',
				'display_name' 	=> __( 'Teaching Assistant', \UBC\Press::get_text_domain() ),
				'capabilities' 	=> array(
					'read' => true,
					'edit_posts' => true,
					'delete_posts' => true,
					'upload_files' => true,
					'publish_posts' => true,
					'edit_published_posts' => true,
					'edit_posts' => true,
					'delete_published_posts' => true,
					'delete_posts' => true,
					'read_private_posts' => true,
					'read_private_pages' => true,
					'publish_pages' => true,
					'moderate_comments' => true,
				),
			),

			'instructor' => array(
				'role' 			=> 'instructor',
				'display_name' 	=> __( 'Instructor', \UBC\Press::get_text_domain() ),
				'capabilities' 	=> array(
					'read' => true,
					'edit_posts' => true,
					'delete_posts' => true,
					'upload_files' => true,
					'publish_posts' => true,
					'edit_published_posts' => true,
					'edit_posts' => true,
					'delete_published_posts' => true,
					'delete_posts' => true,
					'read_private_posts' => true,
					'read_private_pages' => true,
					'publish_pages' => true,
					'moderate_comments' => true,
					'delete_others_pages' => true,
					'delete_others_posts' => true,
					'delete_pages' => true,
					'delete_posts' => true,
					'delete_private_pages' => true,
					'delete_private_posts' => true,
					'delete_published_pages' => true,
					'delete_published_posts' => true,
					'edit_others_pages' => true,
					'edit_others_posts' => true,
					'edit_pages' => true,
					'edit_posts' => true,
					'edit_private_pages' => true,
					'edit_private_posts' => true,
					'edit_published_pages' => true,
					'edit_published_posts' => true,
					'manage_categories' => true,
					'manage_links' => true,
					'moderate_comments' => true,
					'publish_pages' => true,
					'publish_posts' => true,
					'read' => true,
					'read_private_pages' => true,
					'read_private_posts' => true,
					'upload_files' => true,
					'switch_themes' => true,
					'list_users' => true,
					'export' => true,
					'import' => true,
					'edit_theme_options' => true,
					'manage_options' => true
				),
			),

		);

		static::$roles = $roles;

	}/* determine() */



	/**
	 * Create the roles
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function create() {

		$roles_to_setup = static::$roles;

		// Sanity check
		if ( empty( $roles_to_setup ) || ! is_array( $roles_to_setup ) ) {
			return;
		}

		foreach ( $roles_to_setup as $role => $role_args ) {

			$role = new \UBC\Press\Roles\Role( $role_args['role'], $role_args['display_name'], $role_args['capabilities'] );

		}

	}/* create() */

}/* class Setup */
