<?php

/**
 * Template for the Choose Reading SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'assignment_post_id'
 * @return null
 */

$post_id = ( isset( $instance['reading_post_id'] ) ) ? absint( $instance['reading_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

// Fetch the content for this handout
$title 		= get_the_title( $post_id );
$permalink = get_permalink( $post_id );
?>

<p>
	<span class="dashicons dashicons-visibility"></span>
	<a href="<?php echo esc_url( $permalink ); ?>" title="<?php the_title_attribute( array( 'post' => $post_id ) ); ?>">
		<?php echo esc_html( $title ); ?>
	</a>
</p>
