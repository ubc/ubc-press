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

<div class="component-wrapper component-forum">

	<div class="forum-content">
		<?php echo do_shortcode( '[bbp-single-topic id=' . get_the_ID() . ']' ); ?>
	</div><!-- .assignment-content -->

</div><!-- .component-wrapper -->
