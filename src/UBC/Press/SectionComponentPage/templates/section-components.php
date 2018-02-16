<?php
/**
 * The template for displaying the general dashboard.
 *
 * @package wp-clf-lite
 */

if ( ! is_user_logged_in() ) {
	return wp_redirect( wp_login_url( home_url( '/me' ) ) );
}

$start_path 		= trailingslashit( dirname( __FILE__ ) );

do_action( 'ubc_press_student_dashboard_pre_header' );

get_header();


$get_component 			= ! empty( $wp_query->query_vars['component'] ) ? $wp_query->query_vars['component'] : '';
$get_component_name = 'hiddenquiz' === $get_component ? 'quizze' : $get_component;
$section_type = UBC_WP_CLF_Lite::get_course_content_name_convention();

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main dashboard">

		<?php tha_entry_before(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php tha_entry_top(); ?>

			<div class="entry-content">

				<header class="entry-header">
					<h1 class="course-title section-w-component">
						<?php echo esc_html( $get_component_name ); ?>s<br />
					</h1>
					<p>
						All <?php echo esc_html( $section_type ); ?>(s) that contain: <?php echo esc_html( $get_component_name ); ?>s.
					</p>
				</header>
				<div class="sections-container component-types">

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'section-list.php', true, false, array() ); ?>

				</div>
				<!-- .sections-container -->

			<?php tha_entry_bottom(); ?>

			</div>

		</article><!-- #post-## -->

		<?php tha_entry_after(); ?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php do_action( 'ubc_press_student_dashboard_pre_footer' ); ?>

<?php get_footer(); ?>
