<?php

/**
 * Template for the Choose Link SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'link_post_id'
 * @return null
 */

$post_id = ( isset( $instance['link_post_id'] ) ) ? absint( $instance['link_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

// Fetch the content for this link
$title 		= get_the_title( $post_id );
$permalink = get_permalink( $post_id );

?>

<p>
	<span class="dashicons dashicons-admin-links"></span>
	<a href="<?php echo esc_url( $permalink ); ?>" title="<?php the_title_attribute( array( 'post' => $post_id ) ); ?>">
		<?php echo esc_html( $title ); ?>
	</a>
</p>
