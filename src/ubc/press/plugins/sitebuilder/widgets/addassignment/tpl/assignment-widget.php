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

$post = get_post( $assignment_post_id );
var_dump( $post );
?>
