<?php

	$data = get_query_var( 'template_data' );
	$url_part 		 = $data['saved_data']['saved_from'];
	$url_component = '#component-' . $data['post_id'];
	$saved_url		 = $url_part . $url_component;
?>

<div class="dash-item">

	<div class="callout">
	<h4><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?> <small><br />Saved on <?php echo esc_html( date( 'l M j, Y', $data['saved_data']['when'] ) ); ?></small></h4>
	<a href="<?php echo esc_url( $saved_url ); ?>" class="button small" target="_blank">View saved</a>
		<!-- .button-group -->
	</div>
	<!-- .callout -->
</div>
