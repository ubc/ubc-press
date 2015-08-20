<?php

namespace UBC\Press\CTs;

class Setup {


	/**
	 * Our intializer which determines and then creates out custom post types
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		add_action( 'ubc_press_after_create_cpt', array( $this, 'ubc_press_after_create_cpt__add_handout_type' ), 10, 4 );

	}/* init() */


	/**
	 * Add our Handout Type taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $label_args - Label arguments for this post type
	 * @param (array) $wp_args - WordPress args for this post type
	 * @param (string) $icon - The dashicon icon string
	 * @param (object) $cpt_object - The created whole CPT Object
	 * @return null
	 */

	public function ubc_press_after_create_cpt__add_handout_type( $label_args, $wp_args, $icon, $cpt_object ) {

		if ( 'handout' !== $cpt_object->post_type_name ) {
			return;
		}

		// Add the Handout Type taxonomy
		$cpt_object->register_taxonomy( 'handout_type', array( 'hierarchical' => false ) );

	}/* ubc_press_after_create_cpt__add_handout_type() */

}/* class Setup */
