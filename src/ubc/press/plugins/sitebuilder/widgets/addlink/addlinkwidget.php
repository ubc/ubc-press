<?php
namespace UBC\Press\Plugins\SiteBuilder\Widgets\AddLink;
/*
Widget Name: Include Link
Description: Add a link to a section
Author: Richard Tape
Author URI: http://ubc.ca/
*/

class AddLinkWidget extends \SiteOrigin_Widget {

	function __construct() {

		// $id, $name, $widget_options, $control_options, $form_options, $base_folder
		parent::__construct(
			'ubc-link',
			__( 'Link', \UBC\Press::get_text_domain() ),
			array(
				'description' => __( 'Select a link to add', \UBC\Press::get_text_domain() ),
				'help' => '#',
				'has_preview' => false,
			),
			array(),
			array(
				'link_post_id' => array(
					'type' => 'select',
					'label' => __( 'Choose Link', \UBC\Press::get_text_domain() ),
					'prompt' => __( 'Choose Link', \UBC\Press::get_text_domain() ),
					'options' => \UBC\Press\Plugins\SiteBuilder\Widgets\Utils::get_array_of_posts_for_cpt( 'link' ),
					'state_emitter' => array(
						'callback' => 'set_this_value_to_other',
						'args' => array(
							array( 'fieldtype' => 'select', 'selector' => 'link_post_id' ),
							array( 'fieldtype' => 'input[type="text"]', 'selector' => 'text' ),
						),
					),
				),
				'text' => array(
					'type' => 'text',
					'label' => 'Title',
					'state_handler' => array(
				        'link_post_id[none_found]' => array( 'hide' ),
				        '_else[link_post_id]' => array( 'hide' ),
				    ),
				),
			),
			plugin_dir_path( __FILE__ )
		);

	}

	function initialize() {
	}

	function get_template_name( $instance ) {
		return 'link-widget';
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
siteorigin_widget_register( 'ubc-link', __FILE__, '\UBC\Press\Plugins\SiteBuilder\Widgets\AddLink\AddLinkWidget' );
