jQuery( document ).ready( function( $ ) {

	$( document ).on( 'change', '#ubc_press_onboarding_faculty', change__show_relevant_dept_list );

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

	// Hide all of the department drop-downs
	var all_department_rows = $( '.ubc_press_dept_list' );
	all_department_rows.hide();

	// OK, now fade 'er in
	$( welcome_panel_selector ).fadeTo( 'slow', 1 );


	/**
	 * When the Faculty dropdown is changed, we show the relevant department
	 * dropdown list
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	function change__show_relevant_dept_list() {

		// The faculty dropdown and it's changed-to value
		var faculty_field = $( '#ubc_press_onboarding_faculty' );
		var faculty_value = faculty_field.val();

		// Fetch all department list dropdowns
		var all_department_rows = $( '.ubc_press_dept_list' );

		// The dept list for the chosen faculty
		var chosen_dept_list = $( '#ubc_press_onboarding_' + faculty_value + '_department' );
		var row_of_chosen_dept_list = chosen_dept_list.parents( '.ubc_press_dept_list' );

		// hide all of them, when complete, fade relevant one in
		// setTimeout used to make the animation look a little nicer
		all_department_rows.slideUp( 50, function() {
			window.setTimeout( function(){
				// Fade in the relevant faculty list
				row_of_chosen_dept_list.slideDown( 100 );
			}, 200 );
		} );

	}/* change__show_relevant_dept_list() */

} );
