<?php

/**
 * Template for the Choose Quiz SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'quiz_post_id'
 * @return null
 */

$post_id = ( isset( $instance['quiz_post_id'] ) ) ? absint( $instance['quiz_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

// Not a normal post type, so we can't do that
// \UBC\Press\Plugins\SiteBuilder\Widgets\Utils::show_template_for_post_of_post_type( \UBC\Press::get_plugin_path() . 'src/ubc/press/theme/templates/', 'single-quiz.php', $post_id, 'quiz' );

\UBC\Helpers::locate_template_part_in_plugin( \UBC\Press::get_plugin_path() . 'src/ubc/press/theme/templates/', 'single-quiz.php', true, false, array( 'quiz_post_id' => $post_id ) );
