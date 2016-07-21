<?php
/**
 * Template for the user onboarding
 *
 * @since 1.0.0
 */

?>

<h3 class='ubc-spaces-onboarding-title'><?php esc_html_e( __( 'Welcome to UBC Spaces', \UBC\Press::get_text_domain() ) ); ?></h3>

<p class='ubc-spaces-onboarding-about-description'>
	<?php esc_html_e( __( 'Before we can get started, we need some details from you.', \UBC\Press::get_text_domain() ) ); ?>
</p>

<div class="welcome-panel-column-container">
	<div id="ubc-spaces-onboarding-form"><!-- This is filled via JS from the dasboard widget --></div>
</div><!-- .welcome-panel-column-container -->
