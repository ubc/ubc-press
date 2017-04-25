<?php

	$data = get_query_var( 'template_data' );
?>

<section class="small-12 medium-6 large-4 column">
	<div class="callout">
	<h4><?php echo esc_html( get_the_title( $data['post_id'] ) ); ?></h4>
	<h5>Saved from <a href="<?php echo esc_url( $data['saved_data']['saved_from'] ); ?>"><?php echo esc_url( $data['saved_data']['saved_from'] ); ?></a></h5>
		<div class="button-group tiny">
			<a href="<?php echo esc_url( $data['saved_data']['saved_from'] ); ?>" class="button success tiny">View</a>
		</div>
		<!-- .button-group -->
	</div>
	<!-- .callout -->
</section>
