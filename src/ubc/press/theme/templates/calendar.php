<?php

/**
 * Template for the front-end view of the calendar. The URL to hit this is
 * /calendar/ There's also an option 'mode' for the calendar which can be
 * day/ week/ or month
 *
 * i.e.
 *
 * /calendar/week/ should show the weekly calendar view
 *
 * Default is to show the month view. Leverages JJJ's wp-event-calendar API
 *
 * @since 1.0.0
 *
 */

// Should be one of empty string, day, week or month
$mode = get_query_var( 'mode' );

?>

<?php get_header(); ?>



<?php get_footer(); ?>
