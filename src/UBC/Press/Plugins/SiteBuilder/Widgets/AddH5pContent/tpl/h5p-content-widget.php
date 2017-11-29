<?php

/**
 * Template for the Choose Forum SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'h5p_content_id'
 * @return null
 */

$h5p_content_id 	 = ( isset( $instance['h5p_content_id'] ) ) ? absint( $instance['h5p_content_id'] ) : false;
$h5p_content_title = ( isset( $instance['text'] ) ) ? $instance['text'] : false;


if ( ! $h5p_content_id ) {
	return;
}

\UBC\Helpers::locate_template_part_in_plugin( \UBC\Press::get_plugin_path() . 'src/UBC/Press/Theme/templates/', 'single-h5p.php', true, true, array( 'post_id' => $h5p_content_id, 'title' => $h5p_content_title ) );
