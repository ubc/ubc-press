<?php

namespace UBC\Press\ELDAP;

/**
 * Setup for our ELDAP pieces
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage ELDAP
 *
 */

class Utils {


	public static $connection_options = array();
	public static $password = '';
	public static $uid = '';
	public static $base_dn = '';

	/**
	 * Set up the connection details for ELDAP connections.
	 * This is currently a disaster.
	 * @todo Move this into options or config.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function setup_connection_details() {

		if ( ! defined( 'UBC_PRESS_ELDAP_PASSWORD' ) || empty( constant( 'UBC_PRESS_ELDAP_PASSWORD' ) ) ) {
			die( 'Please define UBC_PRESS_ELDAP_PASSWORD in your wp-config.php file' );
		}

		if ( ! defined( 'UBC_PRESS_ELDAP_UID' ) || empty( constant( 'UBC_PRESS_ELDAP_UID' ) ) ) {
			die( 'Please define UBC_PRESS_ELDAP_UID in your wp-config.php file' );
		}

		// TODO Abstract this abomination
		static::$password	= constant( 'UBC_PRESS_ELDAP_PASSWORD' );
		static::$uid		= constant( 'UBC_PRESS_ELDAP_UID' );

		$user = static::$uid;
		static::$base_dn = 'dc=id,dc=ubc,dc=ca';
		$base_dn = static::$base_dn;
		$base_ou = 'ou=ADMINS,ou=IDM';

		static::$connection_options = array(
			'host' => 'eldapcons.id.ubc.ca',
			'port' => '636',
			'useSsl' => true,
			'bindRequiresDn' => true,
			'username' => "$user,$base_ou,$base_dn",
			'password' => 'HRNe&02S766&',
			'baseDn' => $base_dn,
		);

	}/* setup_connection_details() */


	/**
	 * Build the `cn` for a section. Looks like
	 *
	 * EECE_542_101_2016W
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $data - all the data we need about the course
	 * @return (string|false) The cn for the section or false if one is not makable from the passed data
	 */

	public static function get_cn_for_section( $data ) {

		// First we get all sections for the dept and senate
		$dept	= $data['dept'];
		$senate	= $data['campus'];

		$all_dept_sections = static::get_all_sections_for_dept_in_senate( $dept, $senate );

		// Now we look for the passed section in this list, look directly for the constructed cn first
		$cn_to_test = $dept . '_' . $data['course'] . '_' . $data['section'] . '_' . $data['year'] . $data['session'];

		if ( in_array( $cn_to_test, array_values( $all_dept_sections ), true ) ) {
			return $cn_to_test;
		}

		return false;

	}/* get_cn_for_section() */


	/**
	 * GEt all sections for the passed department in the passed senate
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $dept - The department for which section listings we're getting (i.e. ASTR)
	 * @param (string) $senate - which campus? i.e. UBC
	 * @return (array|string) An array of `cn`s for this dept/senate or false on error
	 */

	public static function get_all_sections_for_dept_in_senate( $dept, $senate ) {

		static::setup_connection_details();

		$ldap = new \Zend\Ldap\Ldap( static::$connection_options );
		$bind = $ldap->bind();

		$base_dn = static::$base_dn;

		$result = $ldap->search(
			'(objectClass=*)',
			"ou=$dept,ou=$senate,ou=ACADEMIC,$base_dn",
			\Zend\Ldap\Ldap::SEARCH_SCOPE_ONE,
			array( 'hasSubordinates' ),
			'',
			null,
			1000,
			30
		);

		$sections = $result->toArray();

		if ( ! $sections || empty( $sections ) || ! is_array( $sections ) ) {
			return false;
		}

		// This should be an array of `[dn]`s which each look like:
		// cn=ASTR_102_L2B_2015W,ou=ASTR,ou=UBC,ou=ACADEMIC,dc=id,dc=ubc,dc=ca
		// We need just the `cn`

		$cns = array();

		foreach ( $sections as $id => $dn_details ) {
			$this_dn = $dn_details['dn'];
			$cn = \UBC\Helpers::get_string_between( $this_dn, 'cn=', ",ou=$dept" );
			$cns[] = $cn;
		}

		return $cns;

	}/* get_all_sections_for_dept_in_senate() */


	/**
	 * Get the classlist for a section. This gets all the `uniquemembers` for a specific section
	 * It then parses the list and just returns the `uid`s
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_classlist_for_section( $cn ) {

		static::setup_connection_details();

		$ldap = new \Zend\Ldap\Ldap( static::$connection_options );
		$bind = $ldap->bind();

		$base_dn = static::$base_dn;

		$result = $ldap->search(
			"(&(objectClass=*)(cn=$cn))",
			"ou=ACADEMIC,$base_dn",
			\Zend\Ldap\Ldap::SEARCH_SCOPE_SUB,
			array(
				'uniqueMember',
			),
			'',
			null,
			1000,
			30
		);

		$users = $result->toArray();

		if ( ! $users || empty( $users ) || ! is_array( $users ) || ! isset( $users[0]['uniquemember'] ) ) {
			return false;
		}

		// This should be an array containing `dn` and `uniquemember`s
		$students = $users[0]['uniquemember'];
		$class_list = array();

		foreach ( $students as $id => $details ) {
			$uid = \UBC\Helpers::get_string_between( $details, 'uid=', ',ou=PEOPLE' );
			$class_list[] = $uid;
		}

		return $class_list;

	}/* get_classlist_for_section() */


	/**
	 * If a user doesn't exist on the platform, we go ahead and create it. This is done in several places
	 * i.e. when an instructor syncs the classlist, or a course site is created and the user list exists,
	 * or when a user signs in for the first time etc.
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $uid - The user ID (i.e. CWL Username)
	 * @return (int) - the ID of the newly created user
	 */

	public static function create_user_and_add_eldap_properties( $uid ) {

		$eldap_properties = static::get_user_data_from_uid( $uid );

		// Bail if username exists
		if ( username_exists( sanitize_text_field( $uid ) ) ) {
			$user = get_user_by( 'login', $uid );
			update_user_meta( $user->ID, 'puid', $eldap_properties['ubceducwlpuid'] );
			return $user->ID;
		}

		$user_data = array(
			'user_login' => sanitize_text_field( $uid ),
			'user_pass' => wp_generate_password( 24, true, true ),
			'user_email' => $eldap_properties['mail'],
			'display_name' => $eldap_properties['givenname'],
			'first_name' => $eldap_properties['givenname'],
			'last_name' => $eldap_properties['sn'],
		);

		$new_user_id = wp_insert_user( $user_data );

		// Now add metadata to this user - PUID
		add_user_meta( $new_user_id, 'puid', $eldap_properties['ubceducwlpuid'] );

		return $new_user_id;

	}/* create_user_and_add_eldap_properties() */

	/**
	 * Make an ELDAP lookup for a specific user and get their properties
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function get_user_data_from_uid( $uid ) {

		static::setup_connection_details();

		$ldap = new \Zend\Ldap\Ldap( static::$connection_options );
		$bind = $ldap->bind();

		$base_dn = static::$base_dn;

		$user_details = $ldap->search(
			"(uid=$uid)",
			"ou=PEOPLE,OU=IDM,$base_dn",
			\Zend\Ldap\Ldap::SEARCH_SCOPE_ONE,
			array(
				'uid',
				'givenName',
				'sn',
				'ubcEduCwlPUID',
				'mail',
			),
			'',
			null,
			1,
			30
		);

		$user_details = $user_details->toArray();

		if ( ! $user_details || empty( $user_details ) || ! is_array( $user_details ) ) {
			return false;
		}

		$user_data = array();

		foreach ( $user_details as $id => $details ) {
			$user_data['givenname'] = $details['givenname'][0];
			$user_data['mail'] = $details['mail'][0];
			$user_data['sn'] = $details['sn'][0];
			$user_data['ubceducwlpuid'] = $details['ubceducwlpuid'][0];
			$user_data['uid'] = $details['uid'][0];
		}

		return $user_data;

	}/* get_user_data_from_uid() */


	/**
	 * Make an ELDAP lookup to retrieve the instructors registered to teach a specific section
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $section - the section for which we want the instructors
	 * @return (array|false) An array of instructors for the passed $section
	 */

	public static function get_instructors_for_section( $section ) {

		static::setup_connection_details();

		$ldap = new \Zend\Ldap\Ldap( static::$connection_options );
		$bind = $ldap->bind();

		$base_dn = static::$base_dn;

		$instructors_list = $ldap->search(
			"(&(objectClass=*)(cn=$section))",
			"ou=INSTRUCTOR,$base_dn",
			\Zend\Ldap\Ldap::SEARCH_SCOPE_SUB,
			array(
				'uniqueMember',
			),
			'',
			null,
			10,
			30
		);

		$instructors_list = $instructors_list->toArray();

		if ( ! $instructors_list || empty( $instructors_list ) || ! is_array( $instructors_list ) ) {
			return false;
		}

		$instructors = array();

		foreach ( $instructors_list[0]['uniquemember'] as $id => $details ) {
			$uid = \UBC\Helpers::get_string_between( $details, 'uid=', ',ou=PEOPLE' );
			$instructors[] = $uid;
		}

		return $instructors;

	}/* get_instructors_for_section() */

}
