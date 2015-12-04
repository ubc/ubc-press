<?php

/**
 * The single template for a quiz is different to most. It is passed an ID of
 * a quiz and then forms a shortcode to output as WP Pro Quiz generates shortcodes
 * for the output of each quiz.
 *
 * The passed post ID is the *fake* hiddenquiz post. We need to fetch the real
 * quiz ID from that which is stored as post meta on that hiddenquiz post as
 * ubc_press_associated_quiz
 *
 * @since 1.0.0
 *
 */

$hidden_quiz_post_id = get_the_ID();
$wp_pro_quiz_id = get_post_meta( $hidden_quiz_post_id, 'ubc_press_associated_quiz', true );

?>

<div class="quiz-content">
	<?php echo do_shortcode( "[WpProQuiz $wp_pro_quiz_id]" ); ?>
</div><!-- .assignment-content -->
