<?php

/**
 * Single template for assignments. Used when someone hits up a single
 * assignment URL or when an assignment component is added to a section.
 * Already in the_loop
 *
 * Assignments are gravity forms which are created through the editor.
 * The assignment post (whose loop we are now in) has a meta field of
 * associated_form_id - we'll then use that to place the appropriate
 * form.
 *
 * @since 1.0.0
 *
 */

$post_id = get_the_ID();
$associated_form_id = get_post_meta( $post_id, 'associated_form_id', true );

?>

<div class="component-wrapper component-assignment">

	<h3><?php the_title(); ?></h3>

	<div class="assignment-content">
		<?php echo do_shortcode( '[gravityform id="' . absint( $associated_form_id ) . '" title="false" description="false" ajax="true"]' ); ?>
	</div><!-- .assignment-content -->

</div><!-- .component-wrapper -->
