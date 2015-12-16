<?php

/**
 * Single template for links. Used when someone hits up a single
 * link URL or when an link component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

$links = \UBC\Press\Utils::get_link_content( $post_id );
if ( empty( $links ) ) {
	return;
}
?>

<h3><?php the_title(); ?></h3>

<div class="link-content">
	<ul class="ubc-press-links">
	<?php foreach ( $links as $id => $link ) : ?>
		<li>
			<p class="link-description"><?php echo wp_kses_post( $link['_link_details_link__description'] ); ?></p>
			<ul class="ubc-press-links-list">
			<?php foreach ( $link['_link_details_link_list'] as $lid => $url ) : ?>
				<li>
					<a href="<?php echo esc_url( $url ); ?>" title=""><?php echo esc_url( $url ); ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</li>
	<?php endforeach; ?>
	</ul>
</div><!-- .assignment-content -->
