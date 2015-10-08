<?php

/**
 * Single template for forums. Used when someone hits up a single
 * forum URL or when an forum component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<h3><?php the_title(); ?></h3>

<div class="forum-content">
	<?php echo wp_kses_post( do_shortcode( '[bbp-single-forum id=' . get_the_ID() . ']' ) ); ?>
</div><!-- .assignment-content -->
