<?php

namespace UBC\Press\Plugins\Members;

/**
 * Members mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Members
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

		// Outputs the tabs wrap and the checkboxes for user groups
		add_action( 'members_cp_meta_box_before', array( $this, 'members_cp_metabox_before__add_tabs_and_groups' ) );

		// Outputs the closing tabs wrap
		add_action( 'members_cp_meta_box_after', array( $this, 'members_cp_metabox_after__close_tabs' ) );

		// Save the additional user-groups meta to this post
		add_action( 'save_post', array( $this, 'save_post__save_user_groups' ), 10, 2 );

		// Add user-group visibility settings to members_can_user_view_post
		add_filter( 'members_can_user_view_post', array( $this, 'members_can_user_view_post__add_user_groups_visibility' ), 15, 3 );

		// Limit the visibility of a sub-section based on a group
		add_filter( 'wp_clf_lite_display_course_section_list_item', array( $this, 'wp_clf_lite_display_course_section_list_item__subsection_listings_visibility' ), 10, 2 );

		add_action( 'admin_head-edit.php', array( $this, 'adjust_section_titles_for_groups' ) );
	}/* setup_actions() */

	/**
	 * Filters to modify items in SiteBuilder
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_filters() */

	/**
	 * Hook into the top of the content permissions metabox created by Members and
	 * output tabs so that we can have custom restrictions
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $post - The current WP Post object
	 *
	 * @return null
	 */

	public function members_cp_metabox_before__add_tabs_and_groups( $post ) {

		$user_groups = get_terms( 'user-group' );

		if ( ! $user_groups || ! is_array( $user_groups ) || empty( $user_groups ) ) {
			echo esc_html_e( 'No user groups set up or users not added to groups.', \UBC\Press::get_text_domain() );

			return;
		}

		// Currently selected user groups for this post
		$post_user_groups = get_post_meta( $post->ID, '_member_access_user_groups', false );

		// We have user groups, so let's output checkboxes for each
		?>
		<div class="members-cp-tabs-wrap">

		<p>
			<?php esc_html_e( 'Limit access to this post\'s content to users of the selected groups.', \UBC\Press::get_text_domain() ); ?>
		</p>
		<div class="members-cp-role-list-wrap">

			<ul class="members-cp-user-group-list">

				<?php foreach ( $user_groups as $id => $term ) : ?>
					<li>
						<label>
							<input type="checkbox"
								   name="members_access_user_group[]" <?php checked( is_array( $post_user_groups ) && in_array( $term->slug, $post_user_groups, true ) ); ?>
								   value="<?php echo esc_attr( $term->slug ); ?>"/>
							<?php echo esc_html( $term->name ); ?>
						</label>
					</li>
				<?php endforeach; ?>

			</ul>
		</div>
		<?php

	}/* members_cp_metabox_before__add_tabs_and_groups() */

	/**
	 * Outputs at the bottom of the content permissions metabox. Closes the
	 * tabs div opened in members_cp_metabox_before__add_tabs_and_groups
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $post - The WP Post Object
	 *
	 * @return null
	 */

	public function members_cp_metabox_after__close_tabs( $post ) {

		$user_groups = get_terms( 'user-group' );

		if ( ! $user_groups || ! is_array( $user_groups ) || empty( $user_groups ) ) {
			return;
		}

		?>
		</div><!-- .members-cp-tabs-wrap -->
		<?php

	}/* members_cp_metabox_after__close_tabs() */

	/**
	 * When a post is saved, we look for the content permissions being saved
	 * and see if there are any user groups, if so, save them
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post being saved
	 * @param string $post - the WP Post object
	 *
	 * @return null
	 */

	public function save_post__save_user_groups( $post_id, $post = '' ) {

		if ( ! is_object( $post ) ) {
			$post = get_post();
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Verify the nonce.
		if ( ! isset( $_POST['members_cp_meta'] ) || ! wp_verify_nonce( $_POST['members_cp_meta'], 'members_cp_meta_nonce' ) ) {
			return;
		}

		$existing_post_user_groups = get_post_meta( $post->ID, '_member_access_user_groups', false );
		$new_post_user_groups      = isset( $_POST['members_access_user_group'] ) ? $_POST['members_access_user_group'] : '';

		if ( is_array( $new_post_user_groups ) ) {
			// If we have an array of new user groups, set them
			$this->set_post_user_groups( $post_id, array_map( array(
				$this,
				'sanitize_user_groups',
			), $new_post_user_groups ) );
		} elseif ( ! empty( $existing_post_user_groups ) ) {
			// Else, if we have current user groups but no new groups, delete them all
			delete_post_meta( $post_id, '_member_access_user_groups' );
		}

	}/* save_post__save_user_groups() */

	/**
	 * Sanitize the user groups submitted. Checks that the term exists
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $group - The group to check
	 *
	 * @return string Validated user group or an empty string
	 */

	public function sanitize_user_groups( $group ) {

		if ( term_exists( $group, 'user-group' ) ) {
			return $group;
		} else {
			return '';
		}

	}/* sanitize_user_groups() */

	/**
	 * Utility method to set a post's access user groups given an array of groups
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 *
	 * @return null
	 */

	public function set_post_user_groups( $post_id, $user_groups ) {

		delete_post_meta( $post_id, '_member_access_user_groups' );

		// Loop through new roles.
		foreach ( $user_groups as $user_group ) {
			add_post_meta( $post_id, '_member_access_user_groups', $user_group, false );
		}

		return null;
	}/* set_post_user_groups() */

	/**
	 * Members provides the members_can_user_view_post method which provides the
	 * members_can_user_view_post filter. By default it only checks user roles.
	 * As we've added the ability for user groups to be added, we also need to
	 * filter the content visibility on the front-end. This method does exactly
	 * that by looking at the post's _member_access_user_groups meta to see if
	 * the author wants to limit by user group. If so, we check the user's user
	 * groups to see if there's a match
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) $can_view - Can the user (with user ID, $user_id) view this post
	 * @param (int) $user_id - The ID of the user we're checking if they can see the post
	 * @param (int) $post_id - The ID of the post we're looking to see if $user_id $can_view
	 *
	 * @return bool Whether the user can see the post
	 */

	public function members_can_user_view_post__add_user_groups_visibility( $can_view, $user_id, $post_id ) {

		// Get the user's user groups. First check the function exists
		if ( ! function_exists( 'wp_get_terms_for_user' ) ) {
			return $can_view;
		}

		// Check if this post has user-group specific restrictions
		$existing_post_user_groups = get_post_meta( $post_id, '_member_access_user_groups', false );

		if ( ! $existing_post_user_groups || ! is_array( $existing_post_user_groups ) || empty( $existing_post_user_groups ) ) {
			return $can_view;
		}
		// OK, there *are* user-group specific content restrictions, let's now
		// see if the passed user is in one of those groups
		$users_user_groups = wp_get_terms_for_user( $user_id, 'user-group' );

		$usable_user_groups = array();
		foreach ( $users_user_groups as $key => $term ) {
			$usable_user_groups[] = $term->slug;
		}

		// Now compare the post's groups with the user's.
		$user_in_group_in_post_restricted_list = false;

		foreach ( $usable_user_groups as $id => $user_group ) {
			if ( in_array( $user_group, $existing_post_user_groups, true ) ) {
				$user_in_group_in_post_restricted_list = true;
			}
		}

		if ( true === $user_in_group_in_post_restricted_list ) {
			return true;
		} else {
			return false;
		}

	}/* members_can_user_view_post__add_user_groups_visibility() */

	/**
	 * The members plugin handles the visibility of individual sub-sections when
	 * attempting to view them, however we need to handle the output of the listing
	 * for sub-sections within a section (i.e. on the main section listings page or
	 * for the sidebar)
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - the current post's ID being displayed.
	 *
	 * @return mixed null if the user can see this sub-section, bool false otherwise.
	 */
	public function wp_clf_lite_display_course_section_list_item__subsection_listings_visibility( $short_circuit, $post_id ) {

		// Find out if this post has group assertions. If not, then bail.
		$post_id = absint( $post_id );

		// Returns single metadata value or array of values
		$post_user_groups = get_post_meta( $post_id, '_member_access_user_groups', false );

		if ( ! $post_user_groups || empty( $post_user_groups ) || ! is_array( $post_user_groups ) ) {
			return null;
		}

		// OK, there are user group assignments. This means that a user must be signed in
		// to see this, so let's check that.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// User is signed in, now check if user is part of group
		$user_id = get_current_user_id();

		// Sanity check
		if ( ! $user_id || empty( $user_id ) ) {
			return false;
		}

		// Admins/TAs/etc. can see it all
		$limit_by_roles = array(
			'student',
			'coursealumnus',
		);

		$limit_by_roles = apply_filters( 'ubc_press_roles_affected_by_content_visibility', $limit_by_roles, $post_id, $user_id );

		if ( ! \UBC\Press\Utils::current_users_role_is_one_of( $limit_by_roles ) ) {
			return null;
		}

		$users_in_groups_allowed = array();

		foreach ( $post_user_groups as $id => $term ) {
			$users_in_groups_allowed[] = wp_get_users_of_group( array( 'term' => $term, 'taxonomy' => 'user-group' ) );
		}

		// No users in groups? Then no access.
		if ( empty( $users_in_groups_allowed ) || ! is_array( $users_in_groups_allowed ) ) {
			return false;
		}

		// Check if the current user is in groups allowed for this content. Default not.
		$user_in_group = false;

		foreach ( $users_in_groups_allowed as $id => $user_objects_array ) {
			foreach ( $user_objects_array as $id => $user_object ) {
				if ( $user_id === $user_object->ID ) {
					$user_in_group = true;
					break;
				}
			}
		}

		if ( false === $user_in_group ) {
			return false;
		}

		// User *is* in group, so let's allow them to see it.
		return null;

	}/* wp_clf_lite_display_course_section_list_item__subsection_listings_visibility() */

	/**
	 * Add a filter to the title when we're displaying items on the admin edit screen.
	 * This allows us to add group assignments to the title of a(ny) post.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 *
	 * @return null
	 */
	public function adjust_section_titles_for_groups() {

		add_filter( 'the_title', array( $this, 'adjust_section_titles_content_for_groups' ), 100, 2 );

		return null;

	}/* manage_section_posts_custom_column__add_groups_to_title() */

	/**
	 * Add group assignments to the title of a post in a WP_List_Table
	 *
	 * @since 1.0.0
	 *
	 * @param string $title - the title of the current post
	 *
	 * @return string Modified title as necessary
	 */
	public function adjust_section_titles_content_for_groups( $title ) {

		$post_id = get_the_ID();
		$post_user_groups = get_post_meta( $post_id, '_member_access_user_groups', false );

		if ( ! $post_user_groups || empty( $post_user_groups ) || ! is_array( $post_user_groups ) ) {
			return $title;
		}

		// OK, so this section has group assignments.
		$output = __( ' (Groups:', 'ubc-press' );

		foreach ( $post_user_groups as $id => $group ) {
			$output .= ', ' . $group;
		}

		$output .= ')';

		return $title . wp_kses_post( $output );

	}
}/* class Setup */
