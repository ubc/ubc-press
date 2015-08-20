<?php

/**
 * Template for the Choose Assignment SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'assignment_post_id'
 * @return null
 */

$assignment_post_id = ( isset( $instance['assignment_post_id'] ) ) ? absint( $instance['assignment_post_id'] ) : false;

if ( ! $assignment_post_id ) {
	return;
}

// Fetch the content for this handout
$title 		= get_the_title( $assignment_post_id );
?>

<p><span class="dashicons dashicons-list-view"></span><?php echo esc_html( $title ); ?></p>
