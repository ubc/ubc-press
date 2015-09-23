<?php

/**
 * Template for the Choose Handout SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'handout_post_id'
 * @return null
 */

$post_id = ( isset( $instance['handout_post_id'] ) ) ? absint( $instance['handout_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

\UBC\Press\Plugins\SiteBuilder\Widgets\Utils::show_template_for_post_of_post_type( \UBC\Press::get_plugin_path() . 'src/ubc/press/theme/templates/', 'single-handout.php', $post_id, 'handout' );
