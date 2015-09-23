<?php

/**
 * Single template for readings. Used when someone hits up a single
 * reading URL or when an reading component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<h3><?php the_title(); ?></h3>

<div class="reading-content">
	<?php the_content(); ?>
</div><!-- .assignment-content -->
