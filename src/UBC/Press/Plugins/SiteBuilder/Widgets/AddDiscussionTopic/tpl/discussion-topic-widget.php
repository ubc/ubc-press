<?php

/**
 * Template for the Choose Topic SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'assignment_post_id'
 * @return null
 */

$post_id = ( isset( $instance['discussion_topic_post_id'] ) ) ? absint( $instance['discussion_topic_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

\UBC\Press\Plugins\SiteBuilder\Widgets\Utils::show_template_for_post_of_post_type( \UBC\Press::get_plugin_path() . 'src/UBC/Press/Theme/templates/', 'single-topic.php', $post_id, 'topic' );
