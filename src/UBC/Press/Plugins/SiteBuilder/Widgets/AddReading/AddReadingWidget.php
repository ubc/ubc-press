<?php
namespace UBC\Press\Plugins\SiteBuilder\Widgets\AddReading;
/*
Widget Name: Include Reading
Description: Add a reading to a section
Author: Richard Tape
Author URI: http://ubc.ca/
*/

if ( ! class_exists( 'SiteOrigin_Widget' ) ) {
	return;
}

class AddReadingWidget extends \SiteOrigin_Widget {

	function __construct() {

		// $id, $name, $widget_options, $control_options, $form_options, $base_folder
		parent::__construct(
			'ubc-reading',
			__( 'Reading', 'ubc-press' ),
			array(
				'description' => __( 'Select a reading to add', 'ubc-press' ),
				'help' => '#',
				'has_preview' => false,
			),
			array(),
			array(
				'reading_post_id' => array(
					'type' => 'select',
					'label' => __( 'Choose Reading', 'ubc-press' ),
					'prompt' => __( 'Choose Reading', 'ubc-press' ),
					'options' => \UBC\Press\Plugins\SiteBuilder\Widgets\Utils::get_array_of_posts_for_cpt( 'reading' ),
					'state_emitter' => array(
						'callback' => 'set_this_value_to_other',
						'args' => array(
							array( 'fieldtype' => 'select', 'selector' => 'reading_post_id' ),
							array( 'fieldtype' => 'input[type="text"]', 'selector' => 'text' ),
						),
					),
				),
				'text' => array(
					'type' => 'text',
					'label' => 'Title',
					'state_handler' => array(
				        'reading_post_id[none_found]' => array( 'hide' ),
				        '_else[reading_post_id]' => array( 'hide' ),
				    ),
				),
			),
			plugin_dir_path( __FILE__ )
		);

	}

	function initialize() {
	}

	function get_template_name( $instance ) {
		return 'reading-widget';
	}

	function get_style_name( $instance ) {
		return 'atom';
	}

	/**
	 * Get the variables that we'll be injecting into the less stylesheet.
	 *
	 * @param $instance
	 *
	 * @return array
	 */
	function get_less_variables( $instance ) {
	}

	/**
	 * Make sure the instance is the most up to date version.
	 *
	 * @param $instance
	 *
	 * @return mixed
	 */
	function modify_instance( $instance ) {
		return $instance;
	}

}

// Param 1 must be the same as first arg in parent::contruct(),
// Param 3 must be specified if class name is not of the form SiteOrigin_Widget_{something}_Widget
siteorigin_widget_register( 'ubc-reading', __FILE__, '\UBC\Press\Plugins\SiteBuilder\Widgets\AddReading\AddReadingWidget' );
