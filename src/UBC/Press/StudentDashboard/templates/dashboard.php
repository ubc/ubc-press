<?php
/**
 * The template for displaying the general dashboard.
 *
 * @package wp-clf-lite
 */

if ( ! is_user_logged_in() ) {
	return wp_redirect( wp_login_url( home_url( '/me' ) ) );
}

do_action( 'ubc_press_student_dashboard_pre_header' );

get_header();

$current_user   = wp_get_current_user();
$course_title		= ( empty( get_theme_mod( 'course_title' ) ) ? 'Please Enter Course Tilte' : get_theme_mod( 'course_title' ) );
$home_url				= home_url( '/' );

$start_path 		= trailingslashit( dirname( __FILE__ ) );

?>

<div id="primary" class="content-area primary">
	<main id="main" class="site-main dashboard">

		<?php tha_entry_before(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php tha_entry_top(); ?>

				<header class="entry-header">
					<h1 class="course-title">Course Dashboard <small>for <?php echo esc_html( $course_title ); ?></small></h1>
				</header>

				<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-tabs.php', true, false, array() ); ?>

				<section class="tabs-content" data-tabs-content="course-dashbord-tabs">

					<header class="show-for-sr">
						<h2>Course activities</h2>
					</header>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-progress.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-notes.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-saved.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-groups.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-discussion.php', true, false, array() ); ?>

				</section><!-- .tabs-content -->

			<footer class="entry-footer">

			</footer><!-- .entry-footer -->

			<?php tha_entry_bottom(); ?>

		</article><!-- #post-## -->

		<?php tha_entry_after(); ?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php do_action( 'ubc_press_student_dashboard_pre_footer' ); ?>

<?php get_footer(); ?>
