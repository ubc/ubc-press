<?php

	$data = get_query_var( 'template_data' );

?>

<div class="dash-item column-12">

	<div class="callout">

		<h4 class="note-meta"><small>Note from: </small> "<a href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>#notes-tab"><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?></a>".</h4>

		<p>
			<small>Last updated: <?php echo esc_html( date( 'l M j, Y', $data['note_data']['when'] ) ); ?></small>
		</p>

		<p>
			<button class="button small success" data-open="note-modal-<?php echo esc_attr( $data['post_id'] ); ?>">Read note</button>
			<a class="button small hollow" href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>#notes-tab" target="_blank">Go to note</a>
		</p>

		<div class="reveal" id="note-modal-<?php echo esc_attr( $data['post_id'] ); ?>" data-reveal>

				<button class="close-button" data-close aria-label="Close modal" type="button">
					<span aria-hidden="true">&times;</span>
				</button>

			<header>
				<p>
					<a href="<?php echo esc_url( get_permalink( $data['post_id'] ) ); ?>#notes-tab"><strong><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?></strong></a><br />
					<small>Last updated: <?php echo esc_html( date( 'l M j, Y', $data['note_data']['when'] ) ); ?></small>
				</p>
			</header>
			<?php echo wp_kses_post( $data['note_data']['content'] ); ?>
		</div>

	</div><!-- .callout -->

</div>
