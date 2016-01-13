<?php

/**
 * Single template for lectures. Used when someone hits up a single
 * lecture URL or when an lecture component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

?>

<div class="component-wrapper component-lecture">

	<h3><?php the_title(); ?></h3>

	<div class="lecture-content">
		<?php the_content(); ?>
	</div><!-- .lecture-content -->

</div><!-- .component-wrapper -->
