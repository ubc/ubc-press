<?php

/**
 * Single template for handouts. Used when someone hits up a single
 * handout URL or when an handout component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<h3><?php the_title(); ?></h3>

<div class="handout-content">
	<?php the_content(); ?>
</div><!-- .assignment-content -->
