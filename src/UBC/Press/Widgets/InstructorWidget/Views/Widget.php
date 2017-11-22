<?php
/**
 * Instructor widget template
 *
 * @since 1.0.0
 *
 */

$title_value 		= ( ! empty( $title ) ? apply_filters( 'widget_title', $instance['title'] ) : '' );
$name_value 		= ( ! empty( $name ) ? $name : '' );
$website_value 		= ( ! empty( $website ) ? '- <a href="'. esc_url( $website ).'">website</a>' : '' );
$email_value 		= ( ! empty( $email ) ? '<a href="mailto:'. esc_html( $email ) .'">'. esc_html( $email ) .'</a>' : '' );
$telephone_value 	= ( ! empty( $telephone ) ? $telephone : '' );
?>

<?php if ( $title ) : ?>

<header>
	<h3 class="widget-title"><?php echo esc_html( $title_value ); ?></h3>
</header>

<?php endif; ?>

<address>
	<p><?php echo esc_html( $name_value ); ?> <?php echo wp_kses_post( $website_value ); ?><br>
	<?php echo esc_html( $telephone_value ); ?><br>
	<?php echo wp_kses_post( $email_value ); ?>
</address>
