jQuery( document ).ready( function( $ ) {

	// The form actually is in a dashboard widget (because it needs to load in the widget context for CMB2)
	var onboarding_form = $( '#ubc_press_onboarding_metabox' );

	// We want to remove the widget entirely, this is the container for the widget
	var dashboard_widget_container = onboarding_form.parents( '.postbox-container' );

	// The template we're replacing the welcome panel with
	var template = ubc_press_onboarding.template;

	// The main selector for the panel
	var welcome_panel_selector = '#welcome-panel';

	// Remove the 'dismiss link'
	$( welcome_panel_selector + ' .welcome-panel-close' ).remove();

	// Empty the container and fill it with the template
	$( welcome_panel_selector).empty().html( template );
	$( '#ubc-spaces-onboarding-form' ).html( onboarding_form );

	// Remove the dashboard widget
	dashboard_widget_container.hide();

	// OK, now fade 'er in
	$( welcome_panel_selector ).fadeTo( 'slow', 1 );

} );
