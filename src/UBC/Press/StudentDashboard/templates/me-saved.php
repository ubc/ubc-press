<?php

	$saved = \UBC\Press\Utils::get_saved_components_for_user_for_site();

?>

<section class="tabs-panel saved" id="panel3v">

	<header>
		<h2>Bookmarks</h2>
		<p class="lead">
			These are the bookmarks you have made across all of the content in this course.
		</p>
	</header>

	<div class="row-expand tabs-content-container">

		<?php
			foreach ( $saved as $post_id => $saved_content ) {
				\UBC\Helpers::locate_template_part_in_plugin( trailingslashit( dirname( __FILE__ ) ), 'me-saved-single.php', true, false, array( 'post_id' => $post_id, 'saved_data' => $saved_content ) );
			}
		?>

	</div>
	<!-- .row -->

</section>
<!-- .saved -->
