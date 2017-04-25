<?php

	$data = get_query_var( 'template_data' );

?>

<section id="note-toggle" class="small-12 medium-12 large-6 column" data-toggler=".expanded">

	<div id="note-toggle" class="callout">

		<p class="note-meta">Filed under "<a href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>"><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?></a>".<br>
		<small>Last updated <?php echo esc_html( $data['note_data']['when'] ); ?></small></p>

		<div class="note">
			<?php echo wp_kses_post( $data['note_data']['content'] ); ?>
		</div><!-- .note -->

	</div><!-- .callout -->

</section>
