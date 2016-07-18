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

	}/* setup_actions() */



	/**
	 * Filters to modify items in SiteBuilder
	 *
	 * @since 1.0.0
	 *
	 * @param null
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
							<input type="checkbox" name="members_access_user_group[]" <?php checked( is_array( $post_user_groups ) && in_array( $term->slug, $post_user_groups ) ); ?> value="<?php echo esc_attr( $term->slug ); ?>" />
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
	 * @param (object) $post - the WP Post object
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
		$new_post_user_groups = isset( $_POST['members_access_user_group'] ) ? $_POST['members_access_user_group'] : '';

		if ( is_array( $new_post_user_groups ) ) {
			// If we have an array of new user groups, set them
			$this->set_post_user_groups( $post_id, array_map( array( $this, 'sanitize_user_groups' ), $new_post_user_groups ) );
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
	 * @return (string) Validated user group or an empty string
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
	 * @return
	 */

	public function set_post_user_groups( $post_id, $user_groups ) {

		delete_post_meta( $post_id, '_member_access_user_groups' );

		// Loop through new roles.
		foreach ( $user_groups as $user_group ) {
			add_post_meta( $post_id, '_member_access_user_groups', $user_group, false );
		}

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
	 * @return (bool) Whether the user can see the post
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
			if ( in_array( $user_group, $existing_post_user_groups ) ) {
				$user_in_group_in_post_restricted_list = true;
			}
		}

		if ( true === $user_in_group_in_post_restricted_list ) {
			return true;
		} else {
			return false;
		}

	}/* members_can_user_view_post__add_user_groups_visibility() */

}/* class Setup */
