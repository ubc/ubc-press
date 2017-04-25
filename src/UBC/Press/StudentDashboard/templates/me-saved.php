<?php

	$saved = \UBC\Press\Utils::get_saved_components_for_user_for_site();

?>

<div class="tabs-panel column saved" id="panel3v">

	<h2>Saved</h2>

	<div class="row">

		<?php
			foreach ( $saved as $post_id => $saved_content ) {
				\UBC\Helpers::locate_template_part_in_plugin( trailingslashit( dirname( __FILE__ ) ), 'me-saved-single.php', true, false, array( 'post_id' => $post_id, 'saved_data' => $saved_content ) );
			}
		?>

	</div>
</div>
