<?php

namespace UBC\Press\Plugins\SiteBuilder\Widgets;

/**
 * Setup for our custom widgets
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Widgets
 *
 */

class Setup {

	/**
	 * An array of registered UBC Press widgets
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var array
	 */

	public static $registered_ubc_press_widgets = array();

	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		// Allow us to add an Assignment to a piece of content
		$this->add_assignment_widget();

		// Add a handout
		$this->add_handout_widget();

		// Add a reading
		$this->add_reading_widget();

		// Add a link
		$this->add_link_widget();

		// Discusion forums (bb-press)
		$this->add_discussion_forum_widget();

		// Inidividual Forum Topic (bbPress)
		$this->add_discussion_topic_widget();

		// Add a lecture
		$this->add_lecture_widget();

		// Add WP Pro Quiz Widget
		$this->add_wp_pro_quiz_widget();

	}/* init() */


	/**
	 * Ensure we're able to extend widgets and, optionally for the existence of
	 * an $other_class
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $other_class - The name of another class to check for existence
	 * @return (bool) True if we have our dependencies
	 */

	public function check_dependencies( $other_class = false ) {

		$default = false;

		if ( ! class_exists( 'SiteOrigin_Widget' ) ) {
			return $default;
		}

		$default = true;

		if ( false === $other_class ) {
			return $default;
		}

		if ( ! class_exists( $other_class ) ) {
			$default = false;
		}

		return $default;

	}/* check_dependencies() */


	/**
	 * Register the Add Assignment Widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_assignment_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddAssignment\AddAssignmentWidget;

		static::$registered_ubc_press_widgets[] = 'AddAssignmentWidget';

	}/* add_assignment_widget() */


	/**
	 * Register the Add HAndout Widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_handout_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddHandout\AddHandoutWidget;

		static::$registered_ubc_press_widgets[] = 'AddHandoutWidget';

	}/* add_handout_widget() */



	/**
	 * Register the Add Reading widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_reading_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddReading\AddReadingWidget;

		static::$registered_ubc_press_widgets[] = 'AddReadingWidget';

	}/* add_reading_widget() */

	/**
	 * Register the Add Link widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */
	public function add_link_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddLink\AddLinkWidget;

		static::$registered_ubc_press_widgets[] = 'AddLinkWidget';

	}/* add_link_widget() */


	/**
	 * If we have bbPress installed, add a widget enabling the display of a
	 * discussion forum as part of a section
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_discussion_forum_widget() {

		if ( ! $this->check_dependencies( 'bbPress' ) ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddDiscussionForum\AddDiscussionForumWidget;

		static::$registered_ubc_press_widgets[] = 'AddDiscussionForumWidget';

	}/* add_discussion_forum_widget() */


	/**
	 * If we have bbPress installed, add a widget enabling the display of a
	 * discussion forum as part of a section
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_discussion_topic_widget() {

		if ( ! $this->check_dependencies( 'bbPress' ) ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddDiscussionTopic\AddDiscussionTopicWidget;

		static::$registered_ubc_press_widgets[] = 'AddDiscussionTopicWidget';

	}/* add_discussion_topic_widget() */

	/**
	 * Register the Add Lecture widget
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */
	public function add_lecture_widget() {

		if ( ! $this->check_dependencies() ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddLecture\AddLectureWidget;

		static::$registered_ubc_press_widgets[] = 'AddLectureWidget';

	}/* add_link_widget() */


	/**
	 * A WP Pro Quiz Widget. WP Pro Quiz has shortcodes to output the quizzes. Our Widget
	 * replicates that meaning people don't need to faff around with shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function add_wp_pro_quiz_widget() {

		if ( ! $this->check_dependencies( 'WpProQuiz_Controller_Admin' ) ) {
			return;
		}

		$widget = new \UBC\Press\Plugins\SiteBuilder\Widgets\AddQuiz\AddQuizWidget;

		static::$registered_ubc_press_widgets[] = 'AddQuizWidget';

	}/* add_wp_pro_quiz_widget() */

}/* class Setup */
