<?php

/**
 * Single template for links. Used when someone hits up a single
 * link URL or when an link component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<h3><?php the_title(); ?></h3>

<div class="link-content">
	<?php the_content(); ?>
</div><!-- .assignment-content -->
