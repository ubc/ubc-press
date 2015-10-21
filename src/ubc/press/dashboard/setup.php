<?php

namespace UBC\Press\Dashboard;

/**
 * Setup for our custom dashboard.
 * Tools is placed into a Setup Menu
 * Profile Menu removed
 * Media menu removed
 * Posts -> Blog
 * Plugins in Setup
 * Settings in Setup
 * Most default dashboard widgets removed
 * Comments -> Blog menu
 *
 * @since 1.0.0
 * @package UBCPress
 * @subpackage Dashboard
 *
 */

class Setup {


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

		if ( ! is_admin() ) {
			return;
		}

		$this->before();

		$this->setup_hooks();

		$this->after();

	}/* init() */


	/**
	 * Setup hooks, actions and filters
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_hooks() {

		$this->setup_actions();

		$this->setup_filters();

	}/* setup_hooks() */


	/**
	 * Set up our add_action() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_actions() {

		// All the actions for editing the default menu are in one method
		$this->edit_default_dashboard_menu();

		// Register our scripts
		add_action( 'init', array( $this, 'init__register_assets' ), 5 );

		// Remove dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup__remove_dashboard_widgets' ), 999 );

		// Remove 'Blog' menu for student role
		add_action( 'admin_menu', array( $this, 'admin_menu__hide_blog_for_students' ), 20 );

		// Remove the WordPress version from the admin footer
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_wp_version' ) );

		// Add logout to the dashboard menu
		add_action( 'admin_menu', array( $this, 'admin_menu__add_logout_to_dashboard' ) );

		// Create the 'Course Options' Page
		add_action( 'admin_init', array( $this, 'admin_init__register_setting' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu__add_course_options_page' ) );

		// Topics/Replies go into the main forums menu
		add_action( 'admin_menu', array( $this, 'admin_menu__move_forum_components' ) );

		// Events should be Calendar and many of the menu items need removing
		add_action( 'admin_menu', array( $this, 'admin_menu__edit_events_menu_for_calendar' ), 15 );

	}/* setup_actions() */


	/**
	 * Set up our add_filter() calls for the dashbaord
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function setup_filters() {

		// Change Howdy
		add_filter( 'gettext', array( $this, 'gettext__change_howdy' ), 10, 3 );

		// Admin footer text
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text__change_footer_text' ) );

	}/* setup_actions() */



	/**
	 * To make this easier, we're putting all of the actions which affect the defaul main menu in one
	 * method - should make it more manageable to edit and turn on/off
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function edit_default_dashboard_menu() {

		// We don't want no stinking admin bar
		add_action( 'init', array( $this, 'init__hide_admin_bar' ), 10 );

		// Tools/Settings menu unnecessary for everyone but those who can manage_options
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_tools_options_menus' ) );

		// Remove Profile from main menu
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_profile' ) );

		// Remove the Media menu from all but those who can manage_options
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_media' ) );

		// Comments now lives in the Blog menu
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_comments' ) );

		// Add a new menu item called 'Blog' which will contain posts/comments
		add_action( 'admin_menu', array( $this, 'admin_menu__add_blog_menu' ), 15 );

		// Now add the submenu items for the Blog menu
		add_action( 'admin_menu', array( $this, 'admin_menu__add_blog_submenu' ), 9999 );

		// Rename 'Pages' to 'Course Info'
		add_action( 'admin_menu', array( $this, 'admin_menu__rename_pages_menu' ) );

		// Adjust the 'Appearance' menu
		add_action( 'admin_menu', array( $this, 'admin_menu__adjust_appearance_menu' ) );

		// Hide the site settings menu for students/tas
		add_action( 'admin_menu', array( $this, 'admin_menu__hide_site_settings_as_appropriate' ) );

		// 'Media' menu becomes 'Files' and shifts down
		add_action( 'admin_menu', array( $this, 'admin_menu__adjust_media_menu' ) );

		// 'Quiz' menu
		add_action( 'admin_menu', array( $this, 'admin_menu__adjust_quiz_menu' ), 100 );

	}/* edit_default_dashboard_menu() */


	/**
	 * Register our assets
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__register_assets() {

		wp_register_style( 'ubc-press-dashboard', \UBC\Press::$plugin_url . 'src/ubc/press/dashboard/assets/css/ubc-press-dashboard.css', null, \UBC\Press::get_version(), 'all' );

		wp_register_script( 'ubc-press-dashboard', \UBC\Press::$plugin_url . 'src/ubc/press/dashboard/assets/js/ubc-press-dashboard.js' );

	}/* init__register_assets() */


	/**
	 * Remove the default dashboard widgets
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function wp_dashboard_setup__remove_dashboard_widgets() {

		global $wp_meta_boxes;

		// wp..
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts'] );

		// bbpress
		unset( $wp_meta_boxes['dashboard']['normal']['core']['bbp-dashboard-right-now'] );

		// yoast seo
		unset( $wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget'] );

		// gravity forms
		unset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] );

	}/* wp_dashboard_setup__remove_dashboard_widgets() */


	/**
	 * Howdy...pardner. You're not in Texas any more.
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function gettext__change_howdy( $translated, $text, $domain ) {

		if ( ! is_admin() || 'default' !== $domain ) {
			return $translated;
		}

		if ( false !== strpos( $translated, 'Howdy' ) ) {
			return str_replace( 'Howdy', 'Welcome', $translated );
		}

		return $translated;

	}/* gettext__change_howdy() */



	/**
	 * Change the admin footer text.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (string) The text displayed in the admin footer
	 */

	public function admin_footer_text__change_footer_text() {

		$version = (string) \UBC\Press::get_version();

		$sign_out_url = wp_logout_url( network_home_url() );
		$sign_out_link = '<a href="' . esc_url( $sign_out_url ) . '" title="' . esc_attr__( 'Sign Out', \UBC\Press::get_text_domain() ) . '">' . esc_attr__( 'Sign Out', \UBC\Press::get_text_domain() ) . '</a>';

		return __( 'UBC Press version ' . $version . ' and powered by WordPress. ' . $sign_out_link, \UBC\Press::get_text_domain() );

	}/* admin_footer_text__change_footer_text() */



	/**
	 * Remove the Tools menu from those who can't manage_options
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__remove_tools_options_menus() {

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );

	}/* admin_menu__remove_tools_options_menus() */



	/**
	 * Remove the 'Profile' menu from the dashboard main menu, it's in the admin bar
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__remove_profile() {

		remove_menu_page( 'profile.php' );

	}/* admin_menu__remove_profile() */



	/**
	 * Remove the main Comments menu from the main menu as it now belongs in blog
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__remove_comments() {

		remove_menu_page( 'edit-comments.php' );

	}/* admin_menu__remove_comments() */


	/**
	 * Remove the 'Media' menu item from those who can't manage_options
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__remove_media() {

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		remove_menu_page( 'upload.php' );

	}/* admin_menu__remove_media() */



	/**
	 * Rename 'Posts' to Blog. This will then be the home for all posts-related stuff
	 * including comments.
	 *
	 * Also change the icon to be more bloggy.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__add_blog_menu() {

		// In order to do this we actually rename 'posts'
		global $menu;

		// Check this is the posts menu
		if ( 'edit.php' !== $menu[5][2] ) {
			return;
		}

		$menu[5][0] = __( 'Blog', \UBC\Press::get_text_domain() ); // Change Posts to Recipes
		$menu[5][6] = 'dashicons-welcome-write-blog';

		// Shuffle it down the list, too.
		$menu[40] = $menu[5];
		unset( $menu[5] );

	}/* admin_menu__add_blog_menu() */


	/**
	 * The sub menu items for the blogs menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__add_blog_submenu() {

		global $submenu;

		// Add comments to the Blog menu
		$submenu['edit.php'][7] = array(
			__( 'Comments', \UBC\Press::get_text_domain() ),
			'edit_posts',
			'edit-comments.php',
		);

	}/* admin_menu__add_blog_submenu() */



	/**
	 * Hide the 'Blog' menu for student role in the admin. It's normally at '5' but
	 * we change it to 40 in admin_menu__add_blog_menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__hide_blog_for_students() {

		if ( ! \UBC\Press\Utils::current_user_has_role( 'student' ) ) {
			return;
		}

		global $menu;

		// Check this is the posts menu
		if ( 'edit.php' !== $menu[40][2] ) {
			return;
		}

		$menu[40][0] = false;
		$menu[40][1] = false;
		$menu[40][2] = false;
		$menu[40][3] = false;
		$menu[40][4] = false;
		$menu[40][5] = false;
		$menu[40][6] = false;

	}/* admin_menu__hide_blog_for_students() */


	/**
	 * Hide the Site Settings menu for those roles who shouldn't see it
	 * It's at position 60
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__hide_site_settings_as_appropriate() {

		if ( \UBC\Press\Utils::current_users_role_is_one_of( array( 'instructor', 'administrator' ) ) ) {
			return;
		}

		if ( is_super_admin() ) {
			return;
		}

		global $menu;

		// Check this is the site settings menu
		if ( 'themes.php' !== $menu[60][2] ) {
			return;
		}

		$menu[60][0] = false;
		$menu[60][1] = false;
		$menu[60][2] = false;
		$menu[60][3] = false;
		$menu[60][4] = false;
		$menu[60][5] = false;
		$menu[60][6] = false;

	}/* admin_menu__hide_site_settings_as_appropriate() */



	/**
	 * Rename the pages menu item to be 'Course Info'
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__rename_pages_menu() {

		// In order to do this we actually rename 'Pages'
		global $menu;

		// Check this is the pages menu
		if ( 'edit.php?post_type=page' !== $menu[20][2] ) {
			return;
		}

		$menu[20][0] = __( 'Course Info', \UBC\Press::get_text_domain() ); // Change Posts to Recipes

		// Shuffle it down
		$menu[50] = $menu[20];
		unset( $menu[20] );

	}/* admin_menu__rename_pages_menu() */



	/**
	 * Adjust the appearance menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__adjust_appearance_menu() {

		global $menu, $submenu;

		// Rename Appearance to Site Settings
		$menu[60][0] = __( 'Site Settings', \UBC\Press::get_text_domain() );

		// Hide the normal 'Settings' menu
		unset( $menu[80] );

		// Hide the normal 'Users' menu
		unset( $menu[70] );

		// Hide the normal 'Tools' menu
		unset( $menu[75] );

		// Hide the plugins menu
		unset( $menu[65] );

		// Put the Users/Tools/Settings Menus into Site Settings
		$submenu['themes.php'][110] = array(
			__( 'General', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-general.php',
		);

		$submenu['themes.php'][115] = array(
			__( 'Writing', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-writing.php',
		);

		$submenu['themes.php'][120] = array(
			__( 'Reading', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-reading.php',
		);

		$submenu['themes.php'][125] = array(
			__( 'Discussion', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-discussion.php',
		);

		$submenu['themes.php'][130] = array(
			__( 'Media', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-media.php',
		);

		$submenu['themes.php'][140] = array(
			__( 'Permalinks', \UBC\Press::get_text_domain() ),
			'manage_options',
			'options-permalink.php',
		);

		$submenu['themes.php'][141] = array(
			__( 'Akismet', \UBC\Press::get_text_domain() ),
			'manage_options',
			'akismet-key-config',
			'Akismet',
		);

		$submenu['themes.php'][210] = array(
			__( 'Import', \UBC\Press::get_text_domain() ),
			'import',
			'import.php',
		);

		$submenu['themes.php'][215] = array(
			__( 'Export', \UBC\Press::get_text_domain() ),
			'export',
			'export.php',
		);

		$submenu['themes.php'][315] = array(
			__( 'Users', \UBC\Press::get_text_domain() ),
			'list_users',
			'users.php',
		);

		$submenu['themes.php'][325] = array(
			__( 'Plugins', \UBC\Press::get_text_domain() ),
			'activate_plugins',
			'plugins.php',
		);

		// Remove 'Themes'
		unset( $submenu['themes.php'][5] );

		// Remove 'Header' menu
		unset( $submenu['themes.php'][15] );

		// Remove 'Background' menu
		unset( $submenu['themes.php'][20] );

	}/* admin_menu__adjust_appearance_menu() */


	/**
	 * Organize the forums menu
	 * Topics and Replies get moved into the main Forums menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__move_forum_components() {

		global $menu, $submenu;

		// Add topic to forum menu
		$submenu['edit.php?post_type=forum'][20] = array(
			0 => __( 'All Topics', \UBC\Press::get_text_domain() ),
			'edit_topics',
			'edit.php?post_type=topic'
		);

		// Add replies to forum menu
		$submenu['edit.php?post_type=forum'][25] = array(
			0 => __( 'All Replies', \UBC\Press::get_text_domain() ),
			'edit_replies',
			'edit.php?post_type=reply'
		);

		// Remove top level topic menu
		unset( $menu[555556] );

		// Remove top level replies menu
		unset( $menu[555557] );

		// And there's a separator
		unset( $menu[555558] );

	}/* admin_menu__move_forum_components() */


	/**
	 * The 'Events' menu needs to be 'Calendar' and most of the submenu items
	 * need to go away
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__edit_events_menu_for_calendar() {

		global $menu, $submenu;

		if ( 'edit.php?post_type=event' !== $menu[44][2] ) {
			return;
		}

		// Events gets added as '44' but links to the events listing. We need to
		// change it to be Calendar and link to edit.php?post_type=event&page=event-calendar
		$menu[44][0] = __( 'Calendar', \UBC\Press::get_text_domain() );
		$menu[44][2] = 'edit.php?post_type=event&page=event-calendar';

		// Let's move it to the top menu item up
		$menu[5] = $menu[44];
		unset( $menu[44] );

		// Add a separator
		$menu[6] = array( '', 'read', 'separator0', '', 'wp-menu-separator' );

		unset( $submenu['edit.php?post_type=event'][5] );
		unset( $submenu['edit.php?post_type=event'][10] );
		unset( $submenu['edit.php?post_type=event'][15] );
		unset( $submenu['edit.php?post_type=event'][16] );
		unset( $submenu['edit.php?post_type=event'][17] );

	}/* admin_menu__edit_events_menu_for_calendar() */


	/**
	 * Hide the admin bar
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__hide_admin_bar() {

		// We need to load our stylesheet
		wp_enqueue_style( 'ubc-press-dashboard' );

		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'wp_admin_bar_class', '__return_false' );

		$wp_scripts = new \WP_Scripts();
		wp_deregister_script( 'admin-bar' );

		$wp_styles = new \WP_Styles();
		wp_deregister_style( 'admin-bar' );

		remove_action( 'init', 'wp_admin_bar_init' );
		remove_filter( 'init', 'wp_admin_bar_init' );
		remove_action( 'wp_head', 'wp_admin_bar' );
		remove_filter( 'wp_head', 'wp_admin_bar' );
		remove_action( 'wp_footer', 'wp_admin_bar' );
		remove_filter( 'wp_footer', 'wp_admin_bar' );
		remove_action( 'admin_head', 'wp_admin_bar' );
		remove_filter( 'admin_head', 'wp_admin_bar' );
		remove_action( 'admin_footer', 'wp_admin_bar' );
		remove_filter( 'admin_footer', 'wp_admin_bar' );
		remove_action( 'wp_head', 'wp_admin_bar_class' );
		remove_filter( 'wp_head', 'wp_admin_bar_class' );
		remove_action( 'wp_footer', 'wp_admin_bar_class' );
		remove_filter( 'wp_footer', 'wp_admin_bar_class' );
		remove_action( 'admin_head', 'wp_admin_bar_class' );
		remove_filter( 'admin_head', 'wp_admin_bar_class' );
		remove_action( 'admin_footer', 'wp_admin_bar_class' );
		remove_filter( 'admin_footer', 'wp_admin_bar_class' );
		remove_action( 'wp_head', 'wp_admin_bar_css' );
		remove_filter( 'wp_head', 'wp_admin_bar_css' );
		remove_action( 'wp_head', 'wp_admin_bar_dev_css' );
		remove_filter( 'wp_head', 'wp_admin_bar_dev_css' );
		remove_action( 'wp_head', 'wp_admin_bar_rtl_css' );
		remove_filter( 'wp_head', 'wp_admin_bar_rtl_css' );
		remove_action( 'wp_head', 'wp_admin_bar_rtl_dev_css' );
		remove_filter( 'wp_head', 'wp_admin_bar_rtl_dev_css' );
		remove_action( 'admin_head', 'wp_admin_bar_css' );
		remove_filter( 'admin_head', 'wp_admin_bar_css' );
		remove_action( 'admin_head', 'wp_admin_bar_dev_css' );
		remove_filter( 'admin_head', 'wp_admin_bar_dev_css' );
		remove_action( 'admin_head', 'wp_admin_bar_rtl_css' );
		remove_filter( 'admin_head', 'wp_admin_bar_rtl_css' );
		remove_action( 'admin_head', 'wp_admin_bar_rtl_dev_css' );
		remove_filter( 'admin_head', 'wp_admin_bar_rtl_dev_css' );
		remove_action( 'wp_footer', 'wp_admin_bar_js' );
		remove_filter( 'wp_footer', 'wp_admin_bar_js' );
		remove_action( 'wp_footer', 'wp_admin_bar_dev_js' );
		remove_filter( 'wp_footer', 'wp_admin_bar_dev_js' );
		remove_action( 'admin_footer', 'wp_admin_bar_js' );
		remove_filter( 'admin_footer', 'wp_admin_bar_js' );
		remove_action( 'admin_footer', 'wp_admin_bar_dev_js' );
		remove_filter( 'admin_footer', 'wp_admin_bar_dev_js' );
		remove_action( 'locale', 'wp_admin_bar_lang' );
		remove_filter( 'locale', 'wp_admin_bar_lang' );
		remove_action( 'wp_head', 'wp_admin_bar_render', 1000 );
		remove_filter( 'wp_head', 'wp_admin_bar_render', 1000 );
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
		remove_filter( 'wp_footer', 'wp_admin_bar_render', 1000 );
		remove_action( 'admin_head', 'wp_admin_bar_render', 1000 );
		remove_filter( 'admin_head', 'wp_admin_bar_render', 1000 );
		remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );
		remove_filter( 'admin_footer', 'wp_admin_bar_render', 1000 );
		remove_action( 'admin_footer', 'wp_admin_bar_render' );
		remove_filter( 'admin_footer', 'wp_admin_bar_render' );
		remove_action( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render', 1000 );
		remove_filter( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render', 1000 );
		remove_action( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render' );
		remove_filter( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render' );

	}/* init__hide_admin_bar() */



	/**
	 * Add logout link to the dashboard menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__add_logout_to_dashboard() {

		global $menu, $submenu;

		$submenu['index.php'][10] = array(
			__( 'Sign Out', \UBC\Press::get_text_domain() ),
			'read',
			wp_logout_url( network_admin_url() ),
		);

	}/* admin_menu__add_logout_to_dashboard() */


	/**
	 * The WP Version in the admin footer is superfluous
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__remove_wp_version() {

		remove_filter( 'update_footer', 'core_update_footer' );

	}/* admin_menu__remove_wp_version() */



	/**
	 * Rename the media menu to 'Files' and shift it down
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */


	public function admin_menu__adjust_media_menu() {

		global $menu;

		if ( ! isset( $menu[10] ) ) {
			return;
		}

		if ( 'upload.php' !== $menu[10][2] ) {
			return;
		}

		if ( ! isset( $menu[10][0] ) ) {
			return;
		}

		$menu[10][0] = __( 'Files', \UBC\Press::get_text_domain() );

		$menu[80] = $menu[10];
		unset( $menu[10] );

	}/* admin_menu__adjust_media_menu() */


	/**
	 * WP Pro Quiz menu
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	public function admin_menu__adjust_quiz_menu() {

		global $menu, $submenu;

		// http://cmslocalhost.dev/wp-admin/admin.php?page=wpProQuiz
		$menu[55] = array(
			0 => 'Quizzes',
			1 => 'wpProQuiz_show',
			2 => 'admin.php?page=wpProQuiz',
			3 => '',
			4 => 'menu-top menu-icon-star-half',
			5 => 'menu-quizzes',
			6 => 'dashicons-star-half',
		);

	}/* admin_menu__adjust_quiz_menu() */


	/**
	 * Register the Course Settings Options
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_init__register_setting() {
		register_setting( 'ubc_course_settings', 'ubc_course_settings' );
	}/* admin_init__register_setting() */


	/**
	 * Add the course options page ... page
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__add_course_options_page() {

		$course_options_page = add_menu_page( 'Course Settings', 'Course Settings', 'manage_options', 'ubc_course_settings', array( $this, 'admin_page_display' ) );
		add_action( "admin_print_styles-{$course_options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );

	}/* admin_menu__add_course_options_page() */


	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page ubc_course_settings">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( 'ubc_course_settings_metabox', 'ubc_course_settings' ); ?>
		</div>
		<?php

	}/* admin_page_display() */


	/**
	 * Run before we run our dashboard setup. Simply runs an action which we can
	 * hook into should we so wish
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function before() {

		do_action( 'ubc_press_before_dashboard_setup' );

	}/* before() */



	/**
	 * Run an action after we set up the dashboard
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	private function after() {

		do_action( 'ubc_press_after_setup_dashboard' );

	}/* after() */

}/* class Setup */
