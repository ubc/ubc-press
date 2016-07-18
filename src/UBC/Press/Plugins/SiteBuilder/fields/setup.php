<?php

namespace UBC\Press\Plugins\SiteBuilder\Fields;

/**
 * Setup for our custom fields
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Fields
 *
 */

class Setup {

	/**
	 * Array of our custom fields so we can dynamically register class paths and prefixes
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var $custom_fields
	 */

	public static $custom_fields = array();


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		static::$custom_fields = array(
			'CustomPostTypeList'
		);

		$this->register_class_prefixes();

		$this->register_class_paths();

	}/* init() */

	public function register_class_prefixes() {

		add_filter( 'siteorigin_widgets_field_class_prefixes', array( $this, 'siteorigin_widgets_field_class_prefixes__reg_new_prefix' ) );

	}/* register_class_prefixes() */


	/**
	 * Need to register a new class prefix for the custom fields
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $class_prefixes - Defined class prefixes
	 * @return (array) Modified class prefixes
	 */

	public function siteorigin_widgets_field_class_prefixes__reg_new_prefix( $class_prefixes ) {

		foreach ( static::$custom_fields as $key => $field_name ) {
			$class_prefixes[] = '\UBC\Press\Plugins\SiteBuilder\Fields\\' . $field_name . '\\';
		}

		return $class_prefixes;

	}/* siteorigin_widgets_field_class_prefixes__reg_new_prefix() */



	/**
	 * Register class paths for the custom fields
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function register_class_paths() {

		add_filter( 'siteorigin_widgets_field_class_paths', array( $this, 'siteorigin_widgets_field_class_paths__reg_class_paths' ) );

	}/* register_class_paths() */



	/**
	 * Register each class path for each of our fields
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $class_paths - defined class paths
	 * @return $class_paths - modified class paths
	 */

	public function siteorigin_widgets_field_class_paths__reg_class_paths( $class_paths ) {

		foreach ( static::$custom_fields as $key => $field_name ) {
			$class_paths[] = trailingslashit( plugin_dir_path( __FILE__ ) ) . $field_name;
		}

		return $class_paths;

	}/* siteorigin_widgets_field_class_paths__reg_class_paths() */


}/* class Setup */
