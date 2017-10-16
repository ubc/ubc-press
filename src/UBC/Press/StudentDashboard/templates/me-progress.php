<?php

$start_path 		= trailingslashit( dirname( __FILE__ ) );

?>

<div class="tabs-panel notes is-active" id="panel1v">

	<div class="row-expand tabs-content-container">
		<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'course-progress.php', true, false, array() ); ?>
	</div>
	<!-- .row -->

</div>
<!-- .notes -->
