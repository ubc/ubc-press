<?php

/**
 * Template for the Choose Reading SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'assignment_post_id'
 * @return null
 */

$reading_post_id = ( isset( $instance['reading_post_id'] ) ) ? absint( $instance['reading_post_id'] ) : false;

if ( ! $reading_post_id ) {
	return;
}

// Fetch the content for this handout
$title 		= get_the_title( $reading_post_id );

?>

<p><span class="dashicons dashicons-visibility"></span><?php echo esc_html( $title ); ?></p>
