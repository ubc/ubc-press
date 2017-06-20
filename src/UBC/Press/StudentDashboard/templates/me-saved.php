<?php

	$saved = \UBC\Press\Utils::get_saved_components_for_user_for_site();

?>

<div class="tabs-panel saved" id="panel3v">

	<header>
		<h3>Saved</h3>
		<p class="lead">
			These are the saved components you have made across all of the content in this course.
		</p>
	</header>

	<div class="tabs-content-container">

		<?php
			foreach ( $saved as $post_id => $saved_content ) {
				\UBC\Helpers::locate_template_part_in_plugin( trailingslashit( dirname( __FILE__ ) ), 'me-saved-single.php', true, false, array( 'post_id' => $post_id, 'saved_data' => $saved_content ) );
			}
		?>

	</div>
	<!-- .row -->

</div>
<!-- .saved -->
