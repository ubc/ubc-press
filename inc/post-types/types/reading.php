<?php

/**
 * Sets up the reading custom post type
 *
 * Sets up the labels and arguments for the Reading custom post type. Also
 * adds an appropriate icon.
 *
 * @since 1.0.0
 *
 * @package WordPress
 * @subpackage UBC Press
 */


$cpt_label_args = array(
	'post_type_name' 	=> 'reading',
	'singular' 			=> 'Reading',
	'plural' 			=> 'Readings',
	'slug' 				=> 'reading',
);


/**
 * Filters UBC Press Custom Post Type arguments
 *
 * A generic filter for UBC Press Custom Post Type arguments
 *
 * @since 1.0.0
 *
 * @param array $cpt_label_args a filtered associative array of post type args
 */

$cpt_label_args = apply_filters( 'ubc_press_post_type_args',
	/**
	 * Filters the post type args specifically for the reading post type
	 *
	 * An associative array of arguments for the reading custom post type
	 *
	 * @since 1.0.0
	 *
	 * @param array $cpt_label_args array of post type args for the CPT class
	 */
	apply_filters( 'ubc_press_post_type_args_reading', $cpt_label_args )
);

/**
 * Filters UBC Press CPT arguments for the WP args (2nd parameter of the new CPT() call)
 *
 * For all the other arguments for the post type as provided by WordPress
 *
 * @since 1.0.0
 *
 * @param array array() An array of optional params that would be passed to WP to create the CPT
 */

$cpt_wp_args = apply_filters( 'ubc_press_wp_post_type_args',
	/**
	 * Filters the specific WP post type args for the reading post type
	 *
	 * @since 1.0.0
	 *
	 * @param array array() as per http://codex.wordpress.org/Function_Reference/register_post_type#Parameters
	 */
	apply_filters( 'ubc_press_wp_post_type_args_reading', array() )
);

$reading = new CPT( $cpt_label_args, $cpt_wp_args );

/**
 * Run immediately after registering a custom post type
 *
 * @since 1.0.0
 *
 * @param object $reading a CPT object returned by the CPT class
 * @param array $cpt_label_args An associative array of labels for the CPT
 * @param array $cpt_wp_args an array of args passed to register_post_type
 */

do_action( 'ubc_press_after_register_cpt', $reading, $cpt_label_args, $cpt_wp_args );

// Add a menu icon
$reading->menu_icon( 'dashicons-book-alt' );
