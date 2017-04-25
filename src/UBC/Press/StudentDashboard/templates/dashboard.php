<?php
/**
 * The template for displaying the general dashboard.
 *
 * @package wp-clf-lite
 */

if ( ! is_user_logged_in() ) {
	return wp_redirect( wp_login_url( home_url( '/me' ) ) );
}

get_header();

$current_user           = wp_get_current_user();
$course_title			= ( empty( get_theme_mod( 'course_title' ) ) ? 'Please Enter Course Tilte' : get_theme_mod( 'course_title' ) );
$home_url				= home_url( '/' );

$start_path = trailingslashit( dirname( __FILE__ ) );

?>

<div id="primary" class="content-area primary">
	<main id="main" class="site-main dashboard">

		<?php tha_entry_before(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php tha_entry_top(); ?>

			<div class="dashboard-header">
				<div class="row expanded">
					<header class="entry-header column small-12">
						<h1 class="course-title"><a href="<?php echo esc_url( $home_url );?>" title="Course home"><?php echo esc_html( $course_title ); ?></a></h1>
					</header>
					<!-- .entry-header -->
				</div>

				<div class="row expanded meta">

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'course-progress.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'course-instructor.php', true, false, array() ); ?>

				</div>
				<!-- .row.expanded -->
			</div>
			<!-- .dashboard-header -->

			<section class="entry-content">

				<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-tabs.php', true, false, array() ); ?>

				<div class="tabs-content row expanded" data-tabs-content="course-dashbord-tabs">

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-notes.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-saved.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-groups.php', true, false, array() ); ?>

					<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'me-discussion.php', true, false, array() ); ?>

				</div><!-- .tabs-content -->

			</section><!-- .entry-content -->

			<footer class="entry-footer">

			</footer><!-- .entry-footer -->

			<?php tha_entry_bottom(); ?>

		</article><!-- #post-## -->

		<?php tha_entry_after(); ?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php get_footer(); ?>
