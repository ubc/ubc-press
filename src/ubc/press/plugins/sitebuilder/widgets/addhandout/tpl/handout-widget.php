<?php

/**
 * Template for the Choose Handout SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'handout_post_id'
 * @return null
 */

$assignment_post_id = ( isset( $instance['handout_post_id'] ) ) ? absint( $instance['handout_post_id'] ) : false;

if ( ! $assignment_post_id ) {
	return;
}

// Fetch the content for this handout
$content 	= \UBC\Press\Utils::get_handout_content( $assignment_post_id );

// Should now contain $content['fields'], $content['taxonomies']
$fields 	= $content['fields'];
$taxonomies = $content['taxonomies'];
?>
