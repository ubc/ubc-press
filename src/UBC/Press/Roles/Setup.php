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
				'display_name' 	=> __( 'Current Student', 'ubc-press' ),
				'capabilities' 	=> array(
					'read' => true,
				),
			),

			'coursealumnus' => array(
				'role' 			=> 'coursealumnus',
				'display_name' 	=> __( 'Course alumnus', 'ubc-press' ),
				'capabilities' 	=> array(),
			),

			'ta' => array(
				'role' 			=> 'ta',
				'display_name' 	=> __( 'Teaching Assistant', 'ubc-press' ),
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
				'display_name' 	=> __( 'Instructor', 'ubc-press' ),
				'capabilities' 	=> array(
					'read' => true,
					'edit_posts' => true,
					'delete_posts' => true,
					'edit_posts' => true,
					'read_private_posts' => true,
					'read_private_pages' => true,
					'publish_pages' => true,
					'moderate_comments' => true,
					'delete_others_pages' => true,
					'delete_others_posts' => true,
					'delete_pages' => true,
					'delete_private_pages' => true,
					'delete_private_posts' => true,
					'delete_published_pages' => true,
					'delete_published_posts' => true,
					'edit_others_pages' => true,
					'edit_others_posts' => true,
					'edit_pages' => true,
					'edit_private_pages' => true,
					'edit_private_posts' => true,
					'edit_published_pages' => true,
					'edit_published_posts' => true,
					'manage_categories' => true,
					'manage_links' => true,
					'publish_posts' => true,
					'upload_files' => true,
					'list_users' => true,
					'export' => true,
					'import' => true,
					'edit_theme_options' => true,
					'manage_options' => true,
					'read_sections' => true,
					'read_private_sections' => true,
					'edit_sections' => true,
					'edit_sections' => true,
					'edit_others_sections' => true,
					'edit_published_sections' => true,
					'publish_sections' => true,
					'delete_others_sections' => true,
					'delete_private_sections' => true,
					'delete_published_sections' => true,
					'read_lectures' => true,
					'read_private_lectures' => true,
					'edit_lectures' => true,
					'edit_lectures' => true,
					'edit_others_lectures' => true,
					'edit_published_lectures' => true,
					'publish_lectures' => true,
					'delete_others_lectures' => true,
					'delete_private_lectures' => true,
					'delete_published_lectures' => true,
					'read_assignments' => true,
					'read_private_assignments' => true,
					'edit_assignments' => true,
					'edit_assignments' => true,
					'edit_others_assignments' => true,
					'edit_published_assignments' => true,
					'publish_assignments' => true,
					'delete_others_assignments' => true,
					'delete_private_assignments' => true,
					'delete_published_assignments' => true,
					'read_handouts' => true,
					'read_private_handouts' => true,
					'edit_handouts' => true,
					'edit_handouts' => true,
					'edit_others_handouts' => true,
					'edit_published_handouts' => true,
					'publish_handouts' => true,
					'delete_others_handouts' => true,
					'delete_private_handouts' => true,
					'delete_published_handouts' => true,
					'read_readings' => true,
					'read_private_readings' => true,
					'edit_readings' => true,
					'edit_readings' => true,
					'edit_others_readings' => true,
					'edit_published_readings' => true,
					'publish_readings' => true,
					'delete_others_readings' => true,
					'delete_private_readings' => true,
					'delete_published_readings' => true,
					'read_links' => true,
					'read_private_links' => true,
					'edit_links' => true,
					'edit_links' => true,
					'edit_others_links' => true,
					'edit_published_links' => true,
					'publish_links' => true,
					'delete_others_links' => true,
					'delete_private_links' => true,
					'delete_published_links' => true,
					'read_submissions' => true,
					'read_private_submissions' => true,
					'edit_submissions' => true,
					'edit_submissions' => true,
					'edit_others_submissions' => true,
					'edit_published_submissions' => true,
					'publish_submissions' => true,
					'delete_others_submissions' => true,
					'delete_private_submissions' => true,
					'delete_published_submissions' => true,
					'wpProQuiz_add_quiz' => true,
					'wpProQuiz_edit_quiz' => true,
					'wpProQuiz_delete_quiz' => true,
					'wpProQuiz_export' => true,
					'wpProQuiz_import' => true,
					'wpProQuiz_show' => true,
					'wpProQuiz_show_statistics' => true,
					'wpProQuiz_reset_statistics' => true,
					'wpProQuiz_change_settings' => true,
					'wpProQuiz_toplist_edit' => true,
					'gravityforms_api' => true,
					'gravityforms_api_settings' => true,
					'gravityforms_create_form' => true,
					'gravityforms_delete_entries' => true,
					'gravityforms_delete_forms' => true,
					'gravityforms_edit_entries' => true,
					'gravityforms_edit_entry_notes' => true,
					'gravityforms_edit_forms' => true,
					'gravityforms_edit_settings' => true,
					'gravityforms_export_entries' => true,
					'gravityforms_preview_forms' => true,
					'gravityforms_view_entries' => true,
					'gravityforms_view_entry_notes' => true,
					'gravityforms_view_settings' => true,
					'gravityforms_view_updates' => true,
					'view_trash' => true,
					'keep_gate' => true,
					'edit_forums' => true,
					'edit_others_forums' => true,
					'publish_forums' => true,
					'read_private_forums' => true,
					'read_hidden_forums' => true,
					'delete_forums' => true,
					'delete_others_forums' => true,
					'edit_topics' => true,
					'edit_others_topics' => true,
					'publish_topics' => true,
					'read_private_topics' => true,
					'read_hidden_topics' => true,
					'delete_topics' => true,
					'delete_others_topics' => true,
					'edit_replies' => true,
					'edit_others_replies' => true,
					'publish_replies' => true,
					'read_private_replies' => true,
					'delete_replies' => true,
					'delete_others_replies' => true,
					'spectate' => true,
					'restrict_content' => true,
					'participate' => true,
					'moderate' => true,
					'edit_events' => true,
					'edit_others_events' => true,
					'publish_events' => true,
					'read_private_events' => true,
					'create_events' => true,
					'delete_events' => true,
					'delete_private_events' => true,
					'delete_published_events' => true,
					'delete_others_events' => true,
					'edit_private_events' => true,
					'edit_published_events' => true,
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
