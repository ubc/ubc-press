<?php

namespace UBC\Press\WPDashboard;

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
 * @subpackage WPDashboard
 *
 */

class Setup extends \UBC\Press\ActionsBeforeAndAfter {


	/**
	 * Initialize ourselves
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init() {

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

		// Network main site menu
		add_action( 'admin_menu', array( $this, 'admin_menu__edit_network_main_site_dashboard_menu' ), 100 );

		// Register our scripts
		add_action( 'init', array( $this, 'init__register_assets' ), 5 );

		// Load our admin JS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts__load_admin_js' ) );

		// Remove dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup__remove_dashboard_widgets' ), 999 );

		// Lecture date column (rather than published date)
		add_action( 'manage_lecture_posts_custom_column', array( $this, 'manage_lecture_posts_custom_column__date_column' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts__make_lecture_date_sortable' ), 9 );

		// AJAX handler for viewing submissions for an assignment in the admin
		add_action( 'ubcpressajax_admin_view_submissions', array( $this, 'ubcpressajax_admin_view_submissions__process' ) );

		// Custom columns for Assignments
		add_action( 'manage_assignment_posts_custom_column', array( $this, 'manage_assignment_posts_custom_column__custom_columns' ), 10, 2 );

		// Add a button which allows instructors to pull student list into course site
		add_action( 'restrict_manage_users', array( $this, 'restrict_manage_users__import_users_via_ldap' ) );

		// Handle AJAX Request for bulk synching users
		add_action( 'ubcpressajax_admin_sync_users_with_eldap', array( $this, 'ubcpressajax_admin_sync_users_with_eldap__process' ) );

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

		// Admin footer text
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text__change_footer_text' ) );

		// Lecture date column (rather than published date)
		add_filter( 'manage_lecture_posts_columns' , array( $this, 'manage_lecture_posts_columns__date_column' ) );
		add_filter( 'manage_edit-lecture_sortable_columns', array( $this, 'manage_edit_lecture_sortable_columns__make_lecture_date_srotable' ) );

		// Place a get submissions link in the quick actions list for assignments
		add_filter( 'post_row_actions', array( $this, 'post_row_actions__add_get_submissions_link_to_assignments' ), 10, 2 );

		// Custom assignment columns
		add_filter( 'manage_assignment_posts_columns' , array( $this, 'manage_assignment_posts_columns__assignment_columns' ) );

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

		// // Adjust the 'Appearance' menu
		// add_action( 'admin_menu', array( $this, 'admin_menu__adjust_appearance_menu' ) );
		//
		// // Hide the site settings menu for students/tas
		// add_action( 'admin_menu', array( $this, 'admin_menu__hide_site_settings_as_appropriate' ) );
		//
		// 'Media' menu becomes 'Files' and shifts down
		add_action( 'admin_menu', array( $this, 'admin_menu__adjust_media_menu' ) );
		//
		// // 'Quiz' menu
		add_action( 'admin_menu', array( $this, 'admin_menu__adjust_quiz_menu' ), 100 );

		// Remove 'Blog' menu for student role
		add_action( 'admin_menu', array( $this, 'admin_menu__hide_blog_for_students' ), 20 );
		//
		// Remove the WordPress version from the admin footer
		add_action( 'admin_menu', array( $this, 'admin_menu__remove_wp_version' ) );

		// Add logout to the dashboard menu
		add_action( 'admin_menu', array( $this, 'admin_menu__add_logout_to_dashboard' ) );

		// Add view site link to dashboard menu
		add_action( 'admin_menu', array( $this, 'admin_menu__add_view_site_to_dashboard' ) );

		// // Create the 'Course Options' Page
		// add_action( 'admin_init', array( $this, 'admin_init__register_setting' ) );
		// add_action( 'admin_menu', array( $this, 'admin_menu__add_course_options_page' ) );

		// Topics/Replies go into the main forums menu
		add_action( 'admin_menu', array( $this, 'admin_menu__move_forum_components' ) );

		// Events should be Calendar and many of the menu items need removing
		add_action( 'admin_menu', array( $this, 'admin_menu__edit_events_menu_for_calendar' ), 15 );

		// gForms, you're not as important as you think you are
		add_action( 'admin_menu', array( $this, 'admin_menu__gravity_forms_no' ), 100 );

	}/* edit_default_dashboard_menu() */



	/**
	 * A network's main site is different. It's not a 'course' site, rather a place
	 * for network admins to set options and details for the coureses within that
	 * network. As such the main menu is different. We don't need 'quizzes'
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__edit_network_main_site_dashboard_menu() {

		if ( ! \UBC\Press\Utils::current_site_is_main_site_for_network() ) {
			return;
		}

		global $menu;

		if ( ! isset( $menu[55] ) || 'admin.php?page=wpProQuiz' !== $menu[55][2] ) {
			return;
		}

		unset( $menu[55] );

	}/* admin_menu__edit_network_main_site_dashboard_menu() */


	/**
	 * Register our assets
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function init__register_assets() {

		wp_register_style( 'ubc-press-dashboard', \UBC\Press::$plugin_url . 'src/UBC/Press/WPDashboard/assets/css/ubc-press-dashboard.css', null, \UBC\Press::get_version(), 'all' );

		wp_register_script( 'ubc-press-dashboard', \UBC\Press::$plugin_url . 'src/UBC/Press/WPDashboard/assets/js/ubc-press-dashboard.js' );

	}/* init__register_assets() */


	public function admin_enqueue_scripts__load_admin_js( $hook ) {

		wp_enqueue_script( 'ubc-press-dashboard' );

	}/* admin_enqueue_scripts__load_admin_js() */

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
		$sign_out_link = '<a href="' . esc_url( $sign_out_url ) . '" title="' . esc_attr__( 'Sign Out', 'ubc-press' ) . '">' . esc_attr__( 'Sign Out', 'ubc-press' ) . '</a>';

		$message = '';

		// If we're a super admin, it's really useful to know which deploy directory/release we're working out of. Let's add that.
		if ( is_super_admin() && defined( 'UBC_RELEASE_DIR' ) ) {
			$message .= esc_html__( 'Release: ' . UBC_RELEASE_DIR . '. ' );
		}

		$message .= 'UBC Press version ' . $version . ' and powered by WordPress. ' . $sign_out_link;

		return wp_kses_post( apply_filters( 'ubc_press_admin_footer_text', $message ), 'ubc-press' );

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

		$menu[5][0] = __( 'Blog', 'ubc-press' ); // Change Posts to Recipes
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
			__( 'Comments', 'ubc-press' ),
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

		// We don't do this for the main site on the network
		if ( \UBC\Press\Utils::current_site_is_main_site_for_network() ) {
			return;
		}

		// In order to do this we actually rename 'Pages'
		global $menu;

		// Check this is the pages menu
		if ( 'edit.php?post_type=page' !== $menu[20][2] ) {
			return;
		}

		$menu[20][0] = __( 'Course Info', 'ubc-press' );

		// Shuffle it down
		$menu[50] = $menu[20];
		unset( $menu[20] );

	}/* admin_menu__rename_pages_menu() */



	/**
	 * Adjust the appearance menu
	 * @TODO Look at the madness that is this method
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__adjust_appearance_menu() {

		global $menu, $submenu;

		// Rename Appearance to Site Settings
		$menu[60][0] = __( 'Site Settings', 'ubc-press' );

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
			__( 'General', 'ubc-press' ),
			'manage_options',
			'options-general.php',
		);

		$submenu['themes.php'][115] = array(
			__( 'Writing', 'ubc-press' ),
			'manage_options',
			'options-writing.php',
		);

		$submenu['themes.php'][120] = array(
			__( 'Reading', 'ubc-press' ),
			'manage_options',
			'options-reading.php',
		);

		$submenu['themes.php'][125] = array(
			__( 'Discussion', 'ubc-press' ),
			'manage_options',
			'options-discussion.php',
		);

		$submenu['themes.php'][130] = array(
			__( 'Media', 'ubc-press' ),
			'manage_options',
			'options-media.php',
		);

		$submenu['themes.php'][140] = array(
			__( 'Permalinks', 'ubc-press' ),
			'manage_options',
			'options-permalink.php',
		);

		$submenu['themes.php'][141] = array(
			__( 'Akismet', 'ubc-press' ),
			'manage_options',
			'akismet-key-config',
			'Akismet',
		);

		$submenu['themes.php'][210] = array(
			__( 'Import', 'ubc-press' ),
			'import',
			'import.php',
		);

		$submenu['themes.php'][215] = array(
			__( 'Export', 'ubc-press' ),
			'export',
			'export.php',
		);

		$submenu['themes.php'][315] = array(
			__( 'Users', 'ubc-press' ),
			'list_users',
			'users.php',
		);

		$submenu['themes.php'][325] = array(
			__( 'Plugins', 'ubc-press' ),
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
			0 => __( 'All Topics', 'ubc-press' ),
			'edit_topics',
			'edit.php?post_type=topic'
		);

		// Add replies to forum menu
		$submenu['edit.php?post_type=forum'][25] = array(
			0 => __( 'All Replies', 'ubc-press' ),
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

		if ( ! isset( $menu[44] ) || 'edit.php?post_type=event' !== $menu[44][2] ) {
			return;
		}

		// Events gets added as '44' but links to the events listing. We need to
		// change it to be Calendar and link to edit.php?post_type=event&page=event-calendar
		$menu[44][0] = __( 'Calendar', 'ubc-press' );
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
	 * Gravity Forms insists on being at the top. I think otherwise. It needs
	 * to be taught some humility.
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__gravity_forms_no() {

		global $menu;

		if ( ! isset( $menu['16.9'] ) || 'gf_edit_forms' !== $menu['16.9']['2'] ) {
			return;
		}

		$menu[100] = $menu['16.9'];
		unset( $menu['16.9'] );

	}/* admin_menu__gravity_forms_no() */


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
			__( 'Sign Out', 'ubc-press' ),
			'read',
			wp_logout_url( network_admin_url() ),
		);

	}/* admin_menu__add_logout_to_dashboard() */


	/**
	 * Add View Site link to the dashboard menu
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__add_view_site_to_dashboard() {

		global $menu, $submenu;

		$submenu['index.php'][9] = array(
			__( 'View Site', 'ubc-press' ),
			'read',
			home_url(),
		);

	}/* admin_menu__add_view_site_to_dashboard() */


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

		$menu[10][0] = __( 'Files', 'ubc-press' );

		$menu[85] = $menu[10];
		unset( $menu[10] );

	}/* admin_menu__adjust_media_menu() */


	/**
	 * WP Pro Quiz menu as 'Quizzes'
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function admin_menu__adjust_quiz_menu() {

		global $menu, $submenu;

		$menu[555559] = array(
			0 => 'Quizzes',
			1 => 'wpProQuiz_show',
			2 => 'admin.php?page=wpProQuiz',
			3 => '',
			4 => 'menu-top menu-icon-star-half',
			5 => 'menu-quizzes',
			6 => 'dashicons-star-half',
		);

		unset( $menu[55] );

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
	 * Data handler for our assignments custom columns
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $column - Which column are we adding content to
	 * @param (int) $post_id - the ID for the post for each row
	 * @return null
	 */

	public function manage_assignment_posts_custom_column__custom_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'submissions':

				$submissions = get_post_meta( $post_id, 'associated_submissions', true );

				// Build the URL
				$url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'admin_view_submissions', true, false, array( 'post_id' => $post_id ) );

				$link = '<a data-post_id="' . $post_id . '" href="' . $url . '" title="" class="hide-if-no-js ubc-press-view-submissions">' . count( $submissions ) . '</a><span class="spinner"></span>';

				echo ( $link );

			break;

			case 'status':

				// If current date is before start date, 'pending: will open on'
				// If current date > start but < end, 'open'
				// If current date > end, 'closed'
				// @TODO: 4 requests per assignment isn't cool. Defer loading here?
				$start_date = get_post_meta( $post_id, 'ubc_assignment_item_date_item_date', true ); // 02/01/2016
				$start_time = get_post_meta( $post_id, 'ubc_assignment_item_date_item_time_start', true ); // 05:00 AM
				$end_date = get_post_meta( $post_id, 'ubc_assignment_item_date_item_date_closing', true );
				$end_time = get_post_meta( $post_id, 'ubc_assignment_item_date_item_time_end', true );

				$start_datetime = strtotime( $start_date . ' ' . $start_time );
				$end_datetime = strtotime( $end_date . ' ' . $end_time );

				$now = strtotime( 'now' );

				if ( $start_datetime < $now && $now < $end_datetime ) {
					echo wp_kses_post( __( 'Open', 'ubc-press' ) );
				} elseif ( $now < $start_datetime ) {
					echo wp_kses_post( __( 'Pending', 'ubc-press' ) );
				} else {
					echo wp_kses_post( __( 'Closed', 'ubc-press' ) );
				}

			break;

		}

	}/* manage_assignment_posts_custom_column__custom_columns() */

	/**
	 * Add our custom columns for assignments
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $columns - Preset columns
	 * @return (array) Modified columns
	 */

	public function manage_assignment_posts_columns__assignment_columns( $columns ) {

		unset( $columns['date'] );
		$columns['submissions'] = __( 'Submissions', 'ubc-press' );
		$columns['status'] = __( 'Status', 'ubc-press' );

		return $columns;

	}/* manage_assignment_posts_columns__assignment_columns() */


	/**
	 * Output for lecture date column
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $column - Which column are we adding content to
	 * @param (int) $post_id - the ID for the post for each row
	 * @return null
	 */

	public function manage_lecture_posts_custom_column__date_column( $column, $post_id ) {

		switch ( $column ) {

			case 'lecturedate':

				// Stored as post meta
				$lecture_date = get_post_meta( $post_id, 'ubc_item_date_item_date', true );
				$lecture_time_start = get_post_meta( $post_id, 'ubc_item_date_item_time_start', true );

				if ( ! isset( $lecture_date ) ) {
					return;
				}

				echo esc_html( $lecture_date );

			break;

		}

	}/* manage_lecture_posts_custom_column__date_column() */

	/**
	 * Add Lecture Date column
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $columns - Preset columns
	 * @return (array) Modified columns
	 */

	public function manage_lecture_posts_columns__date_column( $columns ) {

		// Date should be Published Date
		unset( $columns['date'] );
		// $columns['date'] = __( 'Published Date', 'ubc-press' );

		$columns['lecturedate'] = __( 'Lecture Date', 'ubc-press' );

		return $columns;

	}/* manage_lecture_posts_columns__date_column() */



	/**
	 * Make the lecture date column sortable
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $columns - Registered columns
	 * @return (array) Modified registered columns
	 */

	public function manage_edit_lecture_sortable_columns__make_lecture_date_srotable( $columns ) {

		$columns['lecturedate'] = 'lecturedate';

		return $columns;

	}/* manage_edit_lecture_sortable_columns__make_lecture_date_srotable() */


	/**
	 * Make lecture date column sortable
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $query - The current WP_Query
	 * @return null
	 */

	public function pre_get_posts__make_lecture_date_sortable( $query ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() || 'lecture' !== $query->get( 'post_type' )  ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		switch ( $orderby ) {

			case 'lecturedate':
			case '':
			default:
				$query->set( 'meta_key', 'ubc_item_date_hidden_timestamp' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'ASC' );
			break;

		}

	}/* pre_get_posts__make_lecture_date_sortable() */



	/**
	 * Add a 'View Submissions' link to the row actions list for assignments
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $actions - Currently set row actions
	 * @param (object) $post - The current WP_Post object for a row
	 * @return (array) Modified actions
	 */


	public function post_row_actions__add_get_submissions_link_to_assignments( $actions, $post ) {

		// Bail early if this isn't assignments
		if ( 'assignment' !== $post->post_type ) {
			return $actions;
		}

		// Only Teachers, TAs and Admins should be able to do this
		if ( ! \UBC\Press\Utils::current_users_role_is_one_of( array( 'administrator', 'instructor', 'ta' ) ) ) {
			return $actions;
		}

		// Build the URL
		$url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'admin_view_submissions', true, false, array( 'post_id' => $post->ID ) );

		$actions['view_submissions'] = '<a data-post_id="' . $post->ID . '" href="' . $url . '" title="" class="hide-if-no-js ubc-press-view-submissions">' . __( 'View Submissions', 'ubc-press' ) . '</a><span class="spinner"></span>';

		return $actions;

	}/* post_row_actions__add_get_submissions_link_to_assignments() */


	/**
	 * AJAX Handler for Viewing the submissions attached to an assignment
	 * in the admin. Triggered when someone clicks on the 'View Assignments'
	 * link
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $request_data - the $_REQUEST data
	 * @return null
	 */

	public function ubcpressajax_admin_view_submissions__process( $request_data ) {

		$post_id = absint( $request_data['post_id'] );

		// Our return data`
		$data = array();

		// An assignment has post meta of associated_submissions
		$associated_submissions = get_post_meta( $post_id, 'associated_submissions', true );

		if ( ! is_array( $associated_submissions ) ) {
			$associated_submissions = array();
		}

		$data['count'] = count( $associated_submissions );
		$data['submissions'] = array();

		foreach ( $associated_submissions as $id => $submission_post_id ) {
			$title	= get_the_title( $submission_post_id );

			// URL needs to look like admin.php?page=gf_entries&view=entry&lid=3&id=2
			// Where id = form ID and lid is entry ID.
			$entry_id	= get_post_meta( $submission_post_id, 'ubc_press_associated_entry_id', true );
			$entry = \GFAPI::get_entry( $entry_id );
			$form_id = $entry['form_id'];
			$url 	= admin_url( "admin.php?page=gf_entries&view=entry&lid=$entry_id&id=$form_id" );
			$graded	= get_post_meta( $submission_post_id, 'submission_grade', true );
			$post 	= get_post( $submission_post_id );
			$author_name = get_the_author_meta( $post->post_author );
			$data['submissions'][] = array( 'title' => $title, 'url' => $url, 'graded' => $graded, 'author' => $author_name );
		}

		$result = true;
		// If we're coming from an AJAX request, send JSON
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

			if ( false === (bool) $result ) {
				wp_send_json_error( array( 'message' => $result ) );
			}

			wp_send_json_success( array(
				'completed' => true,
				'submissions' => $data,
			) );

		} else {

			$redirect_to = ( isset( $request_data['redirect_to'] ) ) ? esc_url( $request_data['redirect_to'] ) : false;

			// Otherwise, something went wrong somewhere, but we should not show a whitescreen, so redirect back
			if ( false !== $redirect_to ) {
				header( 'Location: ' . $redirect_to );
			} else {
				header( 'Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] );
			}
		}

	}/* ubcpressajax_admin_view_submissions__process() */


	/**
	 * Add a button which allows the instructor to batch import the users for the course
	 *
	 * @since 1.0.0
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 * @return null
	 */

	public function restrict_manage_users__import_users_via_ldap( $which ) {

		// Grab the site meta to ensure we're getting the correct course details
		$course_details = get_option( 'ubc_press_course_details', array() );

		// No course details? Run away.
		if ( empty ( $course_details ) ) {
			echo 'Error: No Course Details';
			return;
		}

		// Generate the AJAX URL
		$ajax_url = \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'admin_sync_users_with_eldap', true, null, false );

		echo ' <input type="submit" name="submit" data-ajax_url="' . esc_url( $ajax_url ) . '" data-dept="' . esc_attr( $course_details['department'] ) . '" data-course="' . esc_attr( $course_details['course'] ) . '" data-section="' . esc_attr( $course_details['section'] ) . '" data-year="' . esc_attr( $course_details['year'] ) . '" data-session="' . esc_attr( $course_details['session'] ) . '" data-campus="' . esc_attr( $course_details['campus'] ) . '" id="synch-students-' . $which . '" class="button sync-students-list" value="Sync Student List">';

	}/* restrict_manage_users__import_users_via_ldap() */

	/**
	 * AJAX handler for bull synching users with ELDAP
	 *
	 * @since 1.0.0
	 *
	 * @param (array) $request_data - the $_REQUEST data
	 * @return null
	 */

	public function ubcpressajax_admin_sync_users_with_eldap__process( $request_data ) {

		// Sanitize and then check we have everything
		$dept		= sanitize_text_field( $request_data['dept'] );
		$course		= sanitize_text_field( $request_data['course'] );
		$section	= sanitize_text_field( $request_data['section'] );
		$year		= sanitize_text_field( $request_data['year'] );
		$session	= sanitize_text_field( $request_data['session'] );
		$campus		= sanitize_text_field( $request_data['campus'] );

		// Ensure we have all of them
		$data 			= array( 'dept' => $dept, 'course' => $course, 'section' => $section, 'year' => $year, 'session' => $session, 'campus' => $campus );
		$data_filtered	= array_filter( $data );

		// Assume true
		$result = true;

		if ( $data !== $data_filtered ) {
			$result = false;
		}

		$is_ajax 		= ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) );
		$redirect_to	= ( isset( $request_data['redirect_to'] ) ) ? esc_url( $request_data['redirect_to'] ) : false;

		// Bail early
		if ( false === (bool) $result ) {
			\UBC\Press\Ajax\Utils::send_json_error( 'Not all course details sent.', $redirect_to );
		}

		// OK so we have everything we need to run an ELDAP lookup

		// First, we get the 'cn' for the section
		$cn = \UBC\Press\ELDAP\Utils::get_cn_for_section( $data );

		// And use that cn to get the classlist from ELDAP
		$class_list = \UBC\Press\ELDAP\Utils::get_classlist_for_section( $cn );

		if ( false === $class_list ) {
			\UBC\Press\Ajax\Utils::send_json_error( 'No class list found.', $redirect_to );
		}

		// OK we have a list of CWLs. Now we need to match them up with the current list of users
		// on this site. If the list is the same, we don't do anything.
		$current_user_list = get_users(
			array(
				'blog_id' => get_current_blog_id(),
				'role' => 'student',
				'fields' => array( 'user_login' ),
			)
		);

		$current_users = array();

		if ( $current_user_list && is_array( $current_user_list ) ) {
			foreach ( $current_user_list as $id => $user_object ) {
				$current_users[] = $user_object->user_login;
			}
		}

		// We now have a list of the current site's students and a list of the students that SHOULD be on this site.
		// If they are the same, we do nothing
		$not_on_site = array_diff( $class_list, $current_users );

		if ( empty( $not_on_site ) ) {
			\UBC\Press\Ajax\Utils::send_json_error( 'All users correct.', $redirect_to );
		}

		// $not_on_site contains a list of users in the ELDAP classlist, but NOT on the current site
		// Test if user exists (if not, create) and then add to site as a student
		foreach ( $not_on_site as $id => $username ) {
			$user_id = \UBC\Press\ELDAP\Utils::create_user_and_add_eldap_properties( $username );
			add_user_to_blog( get_current_blog_id(), $user_id, 'student' );
		}

		// Handle the removal of users.
		// $type_of_user_removal = \UBC\Press\

		// Handle the adding of users

		\UBC\Press\Ajax\Utils::send_json_success( array( $class_list, $current_users, $not_on_site ), $redirect_to );

	}/* ubcpressajax_admin_sync_users_with_eldap__process() */

}/* class Setup */
