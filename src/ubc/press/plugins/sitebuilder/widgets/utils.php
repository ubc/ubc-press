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



	/**
	 * The templates for our widgets mainly load a single post of a specified
	 * post type and then load a template for that post type. This is a wrapper
	 * method which does just that
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public static function show_template_for_post_of_post_type( $template_start, $template_path, $post_id, $post_type ) {

		$post_id = absint( $post_id );
		$post_type = sanitize_text_field( $post_type );

		$args = array(
			'post__in' => array( $post_id ),
			'posts_per_page' => 1,
			'post_type' => $post_type,
		);

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) :

			while ( $the_query->have_posts() ) :

				$the_query->the_post();

					\UBC\Helpers::locate_template_part_in_plugin( $template_start, $template_path, true );

				endwhile;
			wp_reset_postdata();
		endif;

	}/* show_template_for_post_of_post_type() */



}/* class Utils */
