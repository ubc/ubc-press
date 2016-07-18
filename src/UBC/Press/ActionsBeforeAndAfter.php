<?php

namespace UBC\Press;


/**
 * A generic class from which many of our classes are extended.
 * This allows us to be more DRY. Actions happen before and after
 * automatically so we can hook in.
 *
 * @since 1.0.0
 *
 */


abstract class ActionsBeforeAndAfter {

	public static $class_path = false;


	/**
	 * Run an action intended to be 'before' anything else the
	 * called class runs. If an action name is passed, use that
	 * rather than auto generating one based on the class path
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $action_name - The action name to run (defaults to using the class path)
	 * @return null
	 */

	public function before( $action_name = false ) {

		$this->do_appropriate_action( $action_name, 'before' );

	}/* before() */

	public function after( $action_name = false ) {

		$this->do_appropriate_action( $action_name, 'after' );

	}/* after() */


	/**
	 * Allows us to be a little more DRY. Both the before() and after() methods
	 * run the same code to run an action, just with a different prefix
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $action_name - The action name to run (defaults to using the class path)
	 * @param (string) $prefix - A prefix for the action name
	 * @return null
	 */

	public function do_appropriate_action( $action_name = false, $prefix = '' ) {

		if ( false !== $action_name ) {

			$action_name = $this->get_usable_action_name( $action_name );
			do_action( $action_name );

		} else {

			$class_name = $this->get_usable_full_class_path();
			do_action( $prefix . $class_name );

		}

	}/* do_appropriate_action() */


	/**
	 * Generate a usable and sanitized action name which conforms with
	 * WP standards (i.e. underscores) and our own coding practices in
	 * this plugin
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $action_name - the passed action name
	 * @return (string) a usable, sanitized action name
	 */

	public function get_usable_action_name( $action_name ) {

		$action_name = sanitize_title_with_dashes( $action_name, false, 'save' );
		$action_name = str_replace( '-', '_', $action_name );

		return $action_name;

	}/* get_usable_action_name() */


	/**
	 * Get the full path (including namespace) of the class
	 * that extended this class
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function get_called_class() {

		if ( false !== static::$class_path ) {
			return static::$class_path;
		}

		$class_path = get_called_class();
		static::$class_path = $class_path;
		return $class_path;

	}/* get_called_class() */


	/**
	 * Get just the name of the class that is called, without the namespace
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return nul
	 */

	public function get_just_class_name() {

		$full_path = $this->get_called_class();

		return substr( strrchr( $full_path, '\\' ), 1 );

	}/* get_just_class_name() */


	/**
	 * Don't really want slashes in an action name, so
	 * let's replace \ and \\ with _ and strtolower so we get some usable
	 * action names
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function get_usable_full_class_path() {

		$full_path = $this->get_called_class();

		return strtolower( str_replace( '\\', '_', str_replace( '\\\\', '_', $full_path ) ) );

	}/* get_usable_full_class_path() */


}/* class ActionsBeforeAndAfter */

?>
