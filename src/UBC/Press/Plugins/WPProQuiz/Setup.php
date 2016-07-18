<?php

namespace UBC\Press\Plugins\WPProQuiz;

/**
 * Setup for our WP Pro Quiz Mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage WPProQuiz
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

		// Quiz is completed, mark as complete for the student
		add_action( 'wp_pro_quiz_completed_quiz', array( $this, 'wp_pro_quiz_completed_quiz__mark_as_completed' ) );

		// Quiz is saved, add a new hiddenquiz
		add_action( 'wp_pro_quiz_save_quiz', array( $this, 'wp_pro_quiz_save_quiz__make_fake_post' ) );

		// Quiz is deleted, delete the aossciated hiddenquiz post
		add_action( 'wp_pro_quiz_delete_quiz', array( $this, 'wp_pro_quiz_delete_quiz__delete_fake_post' ) );

	}/* setup_actions() */



	/**
	 * Filters to modify items in WP Event Calendar
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

	}/* setup_filters() */


	/**
	 * When a quiz is completed, mark it as complete for the user
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function wp_pro_quiz_completed_quiz__mark_as_completed() {

		// Grab the quiz ID
		$quiz_id = isset( $_POST['quizId'] ) ? absint( $_POST['quizId'] ) : false;

		// Bail if it's not available
		if ( false === $quiz_id ) {
			return;
		}

		// Grab the score
		$result = isset( $_POST['results']['comp']['result'] ) ? sanitize_text_field( $_POST['results']['comp']['result'] ) : false;

		// Fetch the associated hiddenquiz post
		$hidden_quiz_post_id = static::get_hidden_quiz_post_for_quiz( $quiz_id );

		if ( false === $hidden_quiz_post_id ) {
			return;
		}

		// Mark it as complete
		\UBC\Press\Utils::set_component_as_complete( $hidden_quiz_post_id, get_current_user_id() );

	}/* wp_pro_quiz_completed_quiz__mark_as_completed() */



	/**
	 * When a WP Pro quiz is made, it's a custom entry in the database.
	 * Let's instead make a post - one that isn't public, queryable,
	 * or has any UI (i.e. a 'fake' post) which allows us to associate
	 * the completion of a quiz with a post and therefore give the user
	 * an indication they have completed it.
	 *
	 * We have a 'hiddenquiz' post type which we create. We then add post
	 * meta to the new post to link the newly created WP Pro Quiz and the
	 * hiddenquiz post.
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $data - A WpProQuiz_Model_Quiz object of the quiz just created
	 * @return null
	 */

	public function wp_pro_quiz_save_quiz__make_fake_post( $data ) {

		if ( ! is_a( $data, 'WpProQuiz_Model_Quiz' ) ) {
			return;
		}

		// Sanitize our data
		$quiz_id	= absint( $data->getId() );
		$quiz_name	= sanitize_text_field( $data->getName() );

		// There's a chance that the hook fires twice, so we need to check if
		// a hiddenquiz post already exists for this quiz ID
		if ( static::hidden_quiz_exists_for_this_quiz( $quiz_id ) ) {
			return;
		}

		// Set up our new post
		$new_post_args = array(
			'post_title' => $quiz_name,
			'post_status' => 'publish',
			'post_type' => 'hiddenquiz',
		);

		// Create it
		$new_hidden_quiz_id = wp_insert_post( $new_post_args );

		// If it's a WP_Error, bail
		if ( is_a( $new_hidden_quiz_id, 'WP_Error' ) ) {
			return;
		}

		// Add our linking post meta
		add_post_meta( $new_hidden_quiz_id, 'ubc_press_associated_quiz', $quiz_id );

	}/* wp_pro_quiz_save_quiz__make_fake_post() */


	/**
	 * When a WP Pro Quiz is deleted, delete the associated hiddenquiz
	 * post
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $quiz_id - The WP Pro quiz ID that has just been deleted
	 * @return null
	 */

	public function wp_pro_quiz_delete_quiz__delete_fake_post( $quiz_id ) {

		$quiz_id = absint( $quiz_id );

		if ( empty( $quiz_id ) ) {
			return;
		}

		$associated_hidden_quiz_post = static::get_hidden_quiz_post_for_quiz( $quiz_id );

		if ( false === $associated_hidden_quiz_post ) {
			return;
		}

		// Trash the post
		wp_delete_post( $associated_hidden_quiz_post, false );

	}/* wp_pro_quiz_delete_quiz__delete_fake_post() */


	/**
	 * Test if a hiddenquiz post already exists for the passed WP Pro Quiz ID
	 * This searches all hiddenquiz posts by meta query looking for
	 * 'ubc_press_associated_quiz' with a value of $quiz_id
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $quiz_id - The WP Pro Quiz ID (stored as ubc_press_associated_quiz)
	 * @return (bool) True if a hiddenquiz post already exists for $quiz_id, false otherwise
	 */

	public static function hidden_quiz_exists_for_this_quiz( $quiz_id ) {

		$hidden_quiz_posts = static::get_hidden_quiz_post_for_quiz( $quiz_id );

		if ( false === $hidden_quiz_posts ) {
			return false;
		}

		return true;

	}/* hidden_quiz_exists_for_this_quiz() */


	/**
	 * Fetch hiddenquiz posts associated with a given WP Pro Quiz ID
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $quiz_id - The WP Pro Quiz ID (stored as ubc_press_associated_quiz)
	 * @return (int|false) The ID of the hiddenquiz post if one exists, false otherwise
	 */

	public static function get_hidden_quiz_post_for_quiz( $quiz_id ) {

		// Sanitize
		$quiz_id = absint( $quiz_id );

		// Build our query args
		$query_args = array(
			'post_type' => 'hiddenquiz',
			'meta_key' => 'ubc_press_associated_quiz',
			'meta_value' => $quiz_id,
		);

		$query = new \WP_Query( $query_args );

		// Found 0 posts? Then no hidden quiz exists for this quiz
		if ( 0 === $query->found_posts ) {
			return false;
		}

		if ( ! is_object( $query->post ) ) {
			return false;
		}

		return $query->post->ID;

	}/* get_hidden_quiz_post_for_quiz() */


}/* class Setup */
