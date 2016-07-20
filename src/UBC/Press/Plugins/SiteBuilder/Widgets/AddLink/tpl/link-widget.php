<?php

/**
 * Template for the Choose Link SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'link_post_id'
 * @return null
 */

$post_id = ( isset( $instance['link_post_id'] ) ) ? absint( $instance['link_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

\UBC\Press\Plugins\SiteBuilder\Widgets\Utils::show_template_for_post_of_post_type( \UBC\Press::get_plugin_path() . 'src/UBC/Press/Theme/templates/', 'single-link.php', $post_id, 'link' );
