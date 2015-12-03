<?php

/**
 * The single template for a quiz is different to most. It is passed an ID of
 * a quiz and then forms a shortcode to output as WP Pro Quiz generates shortcodes
 * for the output of each quiz.
 *
 * @since 1.0.0
 *
 */

$template_data = get_query_var( 'template_data' );
$quiz_post_id = ( isset( $template_data['quiz_post_id'] ) ) ? $template_data['quiz_post_id'] : false;

if ( false === $quiz_post_id ) {
	return;
}

$quiz_post_id = absint( $quiz_post_id );
?>

<div class="quiz-content">
	<?php echo do_shortcode( "[WpProQuiz $quiz_post_id]" ); ?>
</div><!-- .assignment-content -->
