<?php
/**
 * Instructor widget template
 *
 * @since 1.0.0
 *
 */


$widget_title 					= ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : '';

$post_types 				= array( 'handout', 'reading', 'link', 'hiddenquiz', 'assignment', 'lecture', 'forum', 'topic' );
$get_component_slug = 'coursecontent/?component=';

?>

<header>
	<h3 class="widget-title"><?php echo esc_html( $widget_title ); ?></h3>
</header>
	<ul>

		<?php foreach ( $post_types as $post_type ) : ?>

			<?php $get_site_url = get_site_url( null, $get_component_slug . $post_type ); ?>

			<?php $post_type_name = ( 'hiddenquiz' == $post_type  ? 'quizze' : $post_type ); ?>


				<li>
					<a href="<?php echo esc_url( $get_site_url ); ?>" title="See all sub sections with <?php echo esc_attr( $post_type_name ); ?>s"><?php echo esc_html( $post_type_name ); ?>s</a>
				</li>


		<?php endforeach; ?>

	</ul>
