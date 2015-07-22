<?php

// We will have an option to be able to determine which post types are registered, for now
// it's an array, filtered.

/**
 * Filters the available post types
 *
 * The full list of post types that can be made available to a site
 *
 * @since 1.0.0
 *
 * @param array $available_post_types The available post types
 */

$available_post_types = apply_filters( 'ubc_press_available_post_types', array(
	'reading',
	'note',
) );

// Check we have some
if ( ! $available_post_types || ! is_array( $available_post_types ) || empty( $available_post_types ) ) {
	return;
}

foreach ( $available_post_types as $key => $post_type ) {

	$actual_path = self::$plugin_path . 'inc/post-types/types/' . $post_type . '.php';

	if ( ! file_exists( $actual_path ) ) {
		continue;
	}

	require_once $actual_path;
}
