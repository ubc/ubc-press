<?php

	$data = get_query_var( 'template_data' );
	$group_members = \UBC\Press\Utils::get_users_in_group( absint( $data['group_id'] ) );

?>

<section class="small-12 medium-6 large-4 column">

	<div class="callout">

		<h4><?php echo esc_html( $data['group_name'] ); ?></h4>
		<ul>
			<?php foreach ( $group_members as $id => $user ) : ?>
				<li><?php echo wp_kses_post( $user->display_name ); ?></li>
			<?php endforeach; ?>
		</ul>


	</div>
	<!-- .callout -->
</section>
