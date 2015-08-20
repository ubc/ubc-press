<?php

/**
 * Template for the Choose Handout SiteOrigin Widget
 *
 * @since 1.0.0
 *
 * @param (array) $instance - contains data stored for this widget including 'handout_post_id'
 * @return null
 */

$post_id = ( isset( $instance['handout_post_id'] ) ) ? absint( $instance['handout_post_id'] ) : false;

if ( ! $post_id ) {
	return;
}

// Fetch the content for this handout
// $content 	= \UBC\Press\Utils::get_handout_content( $post_id );
$title 		= get_the_title( $post_id );
$permalink = get_permalink( $post_id );

// Should now contain $content['fields'], $content['taxonomies']
// $fields 	= $content['fields']; //
// $taxonomies = $content['taxonomies'];
?>

<p>
	<span class="dashicons dashicons-media-text"></span>
	<a href="<?php echo esc_url( $permalink ); ?>" title="<?php the_title_attribute( array( 'post' => $post_id ) ); ?>">
		<?php echo esc_html( $title ); ?>
	</a>
</p>
