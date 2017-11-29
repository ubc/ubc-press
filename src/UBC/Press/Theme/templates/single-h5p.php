<?php

/**
 * Single template for forums. Used when someone hits up a single
 * forum URL or when an forum component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

// get query var from template data
$data 						 = get_query_var( 'template_data' );
$h5p_content_id 	 = $data['post_id'];
$h5p_content_title = $data['title'];

?>

<div class="component-wrapper component-forum">

	<?php if ( ! empty( $h5p_content_title  ) ) : ?>
		<div class="title">
			<header>
				<h2><?php echo esc_html( $h5p_content_title ); ?></h2>
			</header>
		</div>
	<?php endif; ?>

	<div class="h5p-content">
		<?php echo do_shortcode( '[h5p id="' . absint( $h5p_content_id ) . '"]' ); ?>
	</div><!-- .assignment-content -->

</div><!-- .component-wrapper -->
