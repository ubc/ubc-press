<?php

	$data = get_query_var( 'template_data' );

?>

<div id="note-toggle" class="dash-item">

	<div id="note-toggle" class="callout">

		<h4 class="note-meta"><small>Filed under</small> "<a href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>#section3"><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?></a>".</h4>
		<p>
			<small>Last updated: <?php echo esc_html( date( 'l M j, Y', $data['note_data']['when'] ) ); ?></small></p>
		</p>
		<button data-toggle="note-<?php echo esc_attr( $data['post_id'] ); ?>" class="button small note-button secondary">Read note</button> <a class="button small" href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>#section3" target="_blank">Go to note ></a>
		<div id="note-<?php echo esc_attr( $data['post_id'] ); ?>" class="note" data-toggler=".expanded">
			<?php echo wp_kses_post( $data['note_data']['content'] ); ?>
		</div><!-- .note -->

	</div><!-- .callout -->

</div>
