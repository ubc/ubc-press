<?php

$start_path 		= trailingslashit( dirname( __FILE__ ) );

?>

<section class="tabs-panel user-progress is-active" id="dashboard-progress">

	<header>
		<h2>Your progress</h2>
	</header>
	<div class="row-expand tabs-content-container">
		<?php \UBC\Helpers::locate_template_part_in_plugin( $start_path, 'course-progress.php', true, false, array() ); ?>
	</div>
	<!-- .row -->

</section>
<!-- .notes -->
