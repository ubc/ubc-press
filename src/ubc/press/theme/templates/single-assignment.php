<?php

/**
 * Single template for assignments. Used when someone hits up a single
 * assignment URL or when an assignment component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<h3><?php the_title(); ?></h3>

<div class="assignment-content">
	<?php the_content(); ?>
</div><!-- .assignment-content -->
