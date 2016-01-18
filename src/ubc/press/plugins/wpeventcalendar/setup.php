<?php

namespace UBC\Press\Plugins\WPEventCalendar;

/**
 * Setup for our wp-event-calendar plugin mods
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage WPEventCalendar
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

		// When a lecture/assignment is saved, create/edit a calendar entry if we need to
		add_action( 'save_post', array( $this, 'save_post__link_post_with_calendar' ), 10, 2 );

		// When a lecture is deleted/trashed, we'll delete any calendar entry associated with it
		add_action( 'wp_trash_post', array( $this, 'wp_trash_delete_post__delete_linked_calendar_post' ), 10, 1 );
		add_action( 'wp_delete_post', array( $this, 'wp_trash_delete_post__delete_linked_calendar_post' ), 10, 1 );

		// Add a /calendar/ rewrite rule (so no fake 'page' required)
		add_action( 'init', array( $this, 'init__add_calendar_rewrite_rule' ) );

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

		// Add a /calendar/ rewrite rule (so no fake 'page' required)
		add_filter( 'query_vars', array( $this, 'query_vars__add_calendar_rewrite_rule' ) );
		add_filter( 'template_include', array( $this, 'template_include__add_calendar_rewrite_rule' ) );

	}/* setup_filters() */



	/**
	 * When a lecture is added/saved we look for the date/time metabox info and
	 * add or edit an event as necessary in wp-event-calendar
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of the post being saved
	 * @param (object) $post - the WP Post object
	 * @return null
	 */

	public function save_post__link_post_with_calendar( $post_id, $post = '' ) {

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail early if we're not saving something we add a date to
		$post_type = get_post_type( $post_id );

		$post_types_we_have_dates = array(
			'lecture',
			'assignment',
		);

		if ( ! in_array( $post_type, $post_types_we_have_dates ) ) {
			return;
		}

		if ( ! isset( $_POST['nonce_CMB2phpubc_item_date_metabox'] ) || ! wp_verify_nonce( $_POST['nonce_CMB2phpubc_item_date_metabox'], 'nonce_CMB2phpubc_item_date_metabox' ) ) {
			return;
		}

		// Look for the meta being saved
		$date_key = 'ubc_item_date_item_date';
		$time_key = 'ubc_item_date_item_time';

		// Both not set? Bail
		if ( ! isset( $_POST[ $date_key ] ) && ! isset( $_POST[ $time_key ] ) ) {
			return;
		}

		// We'll need the post ID of the post being saved
		$saved_post_id = absint( $_POST['post_ID'] );

		// Sanitize the date only allowing 0-9 and a forward slash
		$saved_post_date = \UBC\Press\Utils::sanitize_date( $this->get_date_from_post( $_POST ) );

		// Sanitize the time
		$saved_post_time_start = \UBC\Press\Utils::sanitize_time( $this->get_time_from_post( $_POST, 'start' ) );
		$saved_post_time_end = \UBC\Press\Utils::sanitize_time( $this->get_time_from_post( $_POST, 'end' ) );

		// Now we look to see if there's an associated calendar post for this post
		$has_calendar_post = $this->get_associated_calendar_post( $saved_post_id );

		// If there's already a calendar post, we need to check if the date has changed, if so update
		// If there isn't a calendar post, we go ahead and create one
		if ( $has_calendar_post ) {
			$this->check_dates_and_update_if_necessary( $has_calendar_post, $saved_post_date, $saved_post_time_start, $saved_post_time_end );
			return;
		} else {
			$calendar_post_id = $this->create_calendar_post( $saved_post_id, $saved_post_date, $saved_post_time_start, $saved_post_time_end, $post_type );
		}

		// As we've just created a new calendar post we need to associate this post with that new post
		update_post_meta( $saved_post_id, $this->get_associated_calendar_post_meta_key(), $calendar_post_id );

	}/* save_post__link_post_with_calendar() */


	/**
	 * When we save a lecture or assignment post, we check to see if there's already
	 * a calendar entry for this post. If there isn't, we create one.
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $associated_post_id - The ID of the post with which we're associating this calendar post
	 * @param (string) $date - The date of the event
	 * @param (string) $time_start - The start time of this event
	 * @param (string) $time_end - The end time of this event
	 * @param (string) $type_term - Which term are we giving this event (event type)
	 * @return (int) The ID of the calendar post that is created
	 */

	protected function create_calendar_post( $associated_post_id, $date, $time_start, $time_end, $type_term ) {

		$post_content = $post_title = get_the_title( $associated_post_id );

		$create_post_args = array(
			'post_content'	=> $post_content,
			'post_title' 	=> $post_title,
			'post_status' 	=> 'publish',
			'post_type' 	=> $this->get_calendar_post_type(),
		);

		$calendar_post_id = wp_insert_post( $create_post_args );

		// Now we add the term passed in
		wp_set_post_terms( $calendar_post_id, $type_term, $this->get_calendar_event_type() );

		// Build the start and end times. By default if there's no end time, it's the same as the start time
		$start_datetime	= $this->format_datetime( $date, $time_start );
		$end_datetime 	= $this->format_datetime( $date, $time_end );

		// Now we add the custom meta which is the time and date as well as our custom association
		update_post_meta( $calendar_post_id, 'wp_event_calendar_date_time', $start_datetime );
		update_post_meta( $calendar_post_id, 'wp_event_calendar_end_date_time', $end_datetime );
		update_post_meta( $calendar_post_id, sanitize_text_field( $this->get_associated_calendar_post_meta_key() ), $associated_post_id );

		return $calendar_post_id;

	}/* create_calendar_post() */


	/**
	 * If we're updating a lecture/assignment and there's already a calendar post
	 * created for this item then we check that the dates/times haven't been changed
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $calendar_post_id - The calendar post we're checking
	 * @param (string) $date - The date
	 * @param (string) $time_start - Start time
	 * @param (string) $time_end - End time
	 * @return null
	 */

	protected function check_dates_and_update_if_necessary( $calendar_post_id, $date, $time_start, $time_end ) {

		// This is what is stored on the calendar post
		$stored_cal_start_datetime	= get_post_meta( $calendar_post_id, 'wp_event_calendar_date_time', true );
		$stored_cal_end_datetime	= get_post_meta( $calendar_post_id, 'wp_event_calendar_end_date_time', true );

		// This is what has just been submitted on the lecture/assignment (converted to the same format)
		$passed_start_datetime	= $this->format_datetime( $date, $time_start );
		$passed_end_datetime 	= $this->format_datetime( $date, $time_end );

		// Evens stevens? Nothing to do here
		if ( $stored_cal_start_datetime === $passed_start_datetime && $stored_cal_end_datetime === $passed_end_datetime ) {
			return;
		}

		update_post_meta( $calendar_post_id, 'wp_event_calendar_date_time', $passed_start_datetime );
		update_post_meta( $calendar_post_id, 'wp_event_calendar_end_date_time', $passed_end_datetime );

	}/* check_dates_and_update_if_necessary() */

	/**
	 * Add the /calendar/ rewrite rule which will show the front-end rendering
	 * of the course calendar
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__add_calendar_rewrite_rule() {

		// Satisifies [calendar, calendar/, calendar/day, calendar/day/]  (and week and month)
		add_rewrite_rule( 'calendar[\/]?(day|week|month)?[\/]?', 'index.php?pagename=calendar&mode=$matches[1]', 'top' );

	}/* init__add_calendar_rewrite_rule() */

	/**
	 * Add the calendar query var to enable the /calendar/ rewrite
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $vars - Already existing query variables
	 * @return (array) Modified query variables with our added calendar
	 */

	public function query_vars__add_calendar_rewrite_rule( $vars ) {

		$vars[] = __( 'mode', \UBC\Press::get_text_domain() );
		return $vars;

	}/* query_vars__add_calendar_rewrite_rule() */


	/**
	 * If someone hits a calendar URL, we show our calendar template
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function template_include__add_calendar_rewrite_rule( $original_template ) {

		$calendar_page = get_query_var( 'pagename' );

		if ( 'calendar' !== $calendar_page ) {
			return $original_template;
		}

		return \UBC\Press::get_plugin_path() . '/src/ubc/press/theme/templates/calendar.php';

	}/* template_include__add_calendar_rewrite_rule() */


	/**
	 * Format the datetime to be what we expect for the calendar post which is
	 * YYYY-MM-DD HH:MM:SS
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $date - The date part of the datetime
	 * @param (string) $time - The time part of the datetime
	 * @return (string) $datetime - Formatted date time as we expect in our calendar posttype
	 */

	protected function format_datetime( $date, $time ) {

		// Date comes in as MM/DD/YYYY or Unix Timestamp
		// Time comes in as HH:MM (A|P)M
		$converted_time = \DateTime::createFromFormat( 'g:i A', $time )->format( 'H:i:s' );
		// $converted_date = \DateTime::createFromFormat( 'm/d/Y', $date )->format( 'Y-m-d' );

		// Test if we have a timestamp of a date
		if ( \UBC\Press\Utils::is_timestamp( $date ) ) {
			$converted_date = \DateTime::createFromFormat( 'U', $date )->format( 'Y-m-d' );
		} else {
			$converted_date = \DateTime::createFromFormat( 'm/d/Y', $date )->format( 'Y-m-d' );
		}

		// Put them together with a space in between date and time
		$datetime = $converted_date . ' ' . $converted_time;

		return $datetime;

	}/* format_datetime() */


	/**
	 * Return the sanitized date from the $_POST
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $post - The full array from which we wish to return the date
	 * @return (string) The date
	 */

	protected function get_date_from_post( $post ) {

		return \UBC\Press\Utils::get_data_from_post( 'ubc_item_date_item_date' , $post );

	}/* get_date_from_post() */


	/**
	 * Return the sanitized time from the $_POST
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $post - The full array from which we wish to return the time
	 * @return (string) The time
	 */

	protected function get_time_from_post( $post, $start_or_end = 'start' ) {

		if ( 'start' !== $start_or_end && 'end' !== $start_or_end ) {
			$start_or_end = 'start';
		}

		return \UBC\Press\Utils::get_data_from_post( 'ubc_item_date_item_time_' . $start_or_end , $post );

	}/* get_time_from_post() */



	/**
	 * When a lecture post is deleted, if there's a calendar entry associated with it
	 * delete it
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function wp_trash_delete_post__delete_linked_calendar_post( $post_id ) {

		$associated_calendar_post = get_post_meta( $post_id, $this->get_associated_calendar_post_meta_key(), true );

		if ( ! $associated_calendar_post ) {
			return;
		}

		$associated_calendar_post = absint( $associated_calendar_post );

		wp_delete_post( $associated_calendar_post, true );

	}/* wp_trash_delete_post__delete_linked_calendar_post() */


	/**
	 * Get the post ID of an associated calendar post with the passed in $post_id
	 * When we create calendar posts programatically in UBC Press we create extra
	 * meta on the calendar post with the key 'ubc_press_associated_post' we check
	 * for that meta here and return the value if set.
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The post ID of the post we're seeing if there's an associated calendar post
	 * @return (int|false) The post ID of the calendar post if one exists, false otherwise
	 */

	protected function get_associated_calendar_post( $post_id ) {

		// Get our data and sanitize
		$post_id 				= absint( $post_id );
		$post_type 				= sanitize_text_field( $this->get_calendar_post_type() );
		$associated_meta_key	= sanitize_text_field( $this->get_associated_calendar_post_meta_key() );

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		$args = array(
			'posts_per_page'	=> 1,
			'post_type' 		=> $post_type,
			'meta_key' 			=> $associated_meta_key,
			'meta_value' 		=> $post_id,
		);

		/**
		 * Filters the query args to fetch an associated calendar post
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $args - the query args to fetch an associated calendar post
		 */
		$args = apply_filters( 'associated_calendar_post_query_args', $args );

		$query = new \WP_Query( $args );

		if ( $query->found_posts >= 1 ) {
			return $query->post->ID;
		}

		return false;

	}/* get_associated_calendar_post() */


	/**
	 * The slug of the calendar post type. This should allow us to abstract this
	 * out in the future should we wish to change calendar plugins
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The slug of the calendar post type
	 */

	public function get_calendar_post_type() {

		/**
		 * Filters the calendar post type slug
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $calendar_post_type_slug - the calendar post type slug
		 */
		return apply_filters( 'ubc_press_calendar_post_type', 'event' );

	}/* get_calendar_post_type() */


	/**
	 * The slug of the calendar event type (the taxonomy slug). Again this should
	 * allow us to abstract this out.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The slug of the event type taxonomy
	 */

	public function get_calendar_event_type() {

		/**
		 * Filters the calendar event type slug
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $calendar_event_type_slug - the calendar event taxonomy slug
		 */
		 return apply_filters( 'ubc_press_calendar_event_type', 'event-type' );

	}/* get_calendar_event_type() */


	/**
	 * Get the meta key that we use to store the associated calendar post ID in
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The name of the meta key that we use to store the associated cal post
	 */

	public function get_associated_calendar_post_meta_key() {

		/**
		 * Filters the name of the meta key we use to store the associated calendar post
		 *
		 * @since 1.0.0
		 *
		 * @param (string) $associated_calendar_post_meta_key
		 */
		return apply_filters( 'ubc_press_associated_calendar_post_meta_key', 'ubc_press_associated_post' );

	}/* get_associated_calendar_post_meta_key() */

}/* class Setup */
