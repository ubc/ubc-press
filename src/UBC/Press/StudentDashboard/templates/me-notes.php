<?php

$notes = \UBC\Press\Utils::get_user_notes_for_site();

$lead = ( empty( $notes ) ) ? 'You have made no notes for this course.' : 'These are the notes you have made across all of the content in this course.';

?>

<section class="tabs-panel notes is-active" id="dashboard-notes">

	<header>
		<h2>Notes</h2>
		<p class="lead"><?php echo wp_kses_post( $lead ); ?></p>
	</header>

	<div class="row-expand tabs-content-container">
		<?php
		foreach ( $notes as $post_id => $note_content ) {
			\UBC\Helpers::locate_template_part_in_plugin( trailingslashit( dirname( __FILE__ ) ), 'me-notes-single.php', true, false, array( 'post_id' => $post_id, 'note_data' => $note_content ) );
		}
		?>
	</div>
	<!-- .row -->

</section>
<!-- .notes -->
