<?php
namespace UBC\Press\Plugins\SiteBuilder\Widgets\AddH5pContent;
/*
Widget Name: Include H5P content
Description: Add a H5P content to a section
Author: Richard Tape
Author URI: http://ubc.ca/
*/

if ( ! class_exists( 'SiteOrigin_Widget' ) && ! class_exists( 'H5P_Plugin_Admin' ) ) {
	return;
}

class AddH5pContentWidget extends \SiteOrigin_Widget {

	function __construct() {

		// $id, $name, $widget_options, $control_options, $form_options, $base_folder
		parent::__construct(
			'ubc-h5p-content',
			__( 'H5P content', 'ubc-press' ),
			array(
				'description' => __( 'Select H5P content', 'ubc-press' ),
				'help' => '#',
				'has_preview' => false,
			),
			array(),
			array(
				'h5p_content_id' => array(
					'type' => 'select',
					'label' => __( 'Choose H5P content', 'ubc-press' ),
					'prompt' => __( 'Choose H5P content', 'ubc-press' ),
					'options' => \UBC\Press\Plugins\H5p\Setup::ubc_press_get_h5p_content_for_html_select(),
					'state_emitter' => array(
						'callback' => 'set_this_value_to_other',
						'args' => array(
							array( 'fieldtype' => 'select', 'selector' => 'h5p_content_id' ),
							array( 'fieldtype' => 'input[type="text"]', 'selector' => 'text' ),
						),
					),
				),
				'text' => array(
					'type' => 'text',
					'label' => 'Title',
					'state_handler' => array(
								'h5p_content_id[none_found]' => array( 'hide' ),
								'_else[h5p_content_id]' => array( 'hide' ),
						),
				),
			),
			plugin_dir_path( __FILE__ )
		);

	}

	function initialize() {
	}

	function get_template_name( $instance ) {
		return 'h5p-content-widget';
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
siteorigin_widget_register( 'ubc-h5p-content', __FILE__, '\UBC\Press\Plugins\SiteBuilder\Widgets\AddH5pContent\AddH5pContentWidget' );
