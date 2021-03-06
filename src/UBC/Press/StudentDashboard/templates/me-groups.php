<?php

	$groups = \UBC\Press\Utils::get_groups_for_user( get_current_user_id() );

	$lead = ( empty( $groups ) ) ? 'You are a member of no groups in this course.' : 'You are a member of the following groups:';
?>

<section class="tabs-panel" id="dashboard-groups">

	<header>
		<h2>Groups</h2>
		<p class="lead"><?php echo wp_kses_post( $lead ); ?></p>
	</header>

	<div class="row-expand">
		<?php
			foreach ( $groups as $id => $group ) {
				\UBC\Helpers::locate_template_part_in_plugin( trailingslashit( dirname( __FILE__ ) ), 'me-groups-single.php', true, false, array( 'group_id' => $group->term_id, 'group_name' => $group->name ) );
			}
		?>
	</div>

</section>
