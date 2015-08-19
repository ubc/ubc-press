<?php

namespace UBC\Press\Plugins\SiteBuilder\Widgets;

class Utils {


	/**
	 * A method which allows us to fetch a list of custom post types titles and IDs to be
	 * used in a <select> field
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $cpt_name - The name of a CPT
	 * @return (array) An associative array of id => post_title
	 */

	public static function get_array_of_posts_for_cpt( $cpt_name = '' ) {

		$cpt_name = sanitize_text_field( $cpt_name );

		$args = array(
			'posts_per_page' => -1,
			'post_type' => $cpt_name,
		);

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return array( 'none_found', __( 'No ' . $cpt_name . ' found', \UBC\Press::get_text_domain() ) );
		}

		// Start fresh
		$return = array();

		while ( $query->have_posts() ) : $query->the_post();

			$title = get_the_title();
			$post_id = get_the_ID();

			$return[ $post_id ] = $title;

		endwhile;

		wp_reset_postdata();

		return $return;

	}/* get_array_of_posts_for_cpt() */

}/* class Utils */
