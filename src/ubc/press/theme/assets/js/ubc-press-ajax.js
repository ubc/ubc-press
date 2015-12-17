jQuery( document ).ready( function( $ ) {

	$.fn.hasAttr = function(name) {
		return this.attr(name) !== undefined;
	};

	var localized_data = ubc_press_ajax;

	// Mark a component as complete/incomplete when the button is pressed
	$( 'body' ).on( 'click', '.mark-as-complete', click_mark_as_complete__process_completion );

	// Hook in to AJAX success so we can trigger our own items when a quiz is completed
	$( document ).ajaxSuccess( function( event, xhr, settings ) { ajax_success__update_sidebar_completion_counts_for_quiz_completion( event, xhr, settings ); } );

	/**
	 * When a .mark-as-complete button is clicked, we do the appropriate action.
	 * If they've just completed it, we set the user meta (via AJAX), change the button
	 * text and add when it was completed (just now). If they've already completed it and
	 * they are marking it as incomplete, we revert the above.
	 *
	 * @since 1.0.0
	 *
	 * @param (object) event - The click event from jQuery
	 * @return null
	 */

	function click_mark_as_complete__process_completion( event ) {

		event.stopPropagation();
		event.preventDefault();
		var thisButton	= $( this );

		// Do our best to prevent double clicking.
		if ( thisButton.hasAttr( 'disabled' ) || thisButton.hasClass( 'disabled' ) ) {
			return;
		}

		// First things first; disable the buttons
		var allButtons = $( 'a.mark-as-complete' );
		$.each( allButtons, function( i, val ) {
			$( val ).attr( 'disabled', 'disabled' );
			$( val ).addClass( 'disabled' );
		} );

		var originalHref = thisButton.attr( 'href' );

		var url 		= originalHref;
		var post_id 	= thisButton.data( 'post_id' );
		var nonce 		= thisButton.data( 'nonce' );

		thisButton.attr( 'href', '' );

		jQuery.ajax( {
			type : "post",
			dataType : "json",
			url : url,
			data : {
				post_id : post_id,
				nonce: nonce
			},
			beforeSend: function( jqXHR, settings ) {
				start_loading( thisButton );
			},
			success: function( response ) {

				if ( response.success ) {
					switch_completed_state( thisButton, response.data.completed );
					update_progress_bar( response.data.completed );
					update_count_in_section_list( response.data.completed );
				}
			},
			complete: function( jqXHR, textStatus ) {
				stop_loading( thisButton, originalHref, allButtons );
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				return;
			}
		} );

	}/* click_mark_as_complete__process_completion() */


	/**
	 * Switch the completed classes for the button. If it already has a
	 * 'success' class we remove it. If it already has a 'secondary' class
	 * we remove it.
	 *
	 * If we're switching to completed, then add/change the completed message
	 * otherwise remove it
	 *
	 * @since 1.0.0
	 *
	 * @param (object) element - a jQuery object of a HTML element
	 * @param (bool) completed - whether we've just 'completed' or not
	 * @return null
	 */

	function switch_completed_state( element, completed ) {

		element.toggleClass( 'success' );
		element.toggleClass( 'secondary' );

		change_completed_message( element, completed );

	}/* switch_completed_state() */


	/**
	 * Show loading status after a button is pressed.
	 * Change the text to 'loading...' and disable the button
	 *
	 * @since 1.0.0
	 *
	 * @param (object) element - a jQuery object of a HTML element
	 * @return null
	 */

	function start_loading( element ) {

		// Change the text to be 'Loading' but store the current text as a data-attribute
		var currentValue = element.text();

		element.text( localized_data.text.loading );

	}/* start_loading() */


	/**
	 * Stop the loading process
	 * Change the 'loading' text to Completed/Mark as complete, re-enable the button
	 *
	 * @since 1.0.0
	 *
	 * @param (object) element - a jQuery object of a HTML element
	 * @return null
	 */

	function stop_loading( element, originalHref, allButtons ) {

		$.each( allButtons, function( i, val ) {
			$( val ).removeAttr( 'disabled' );
			$( val ).removeClass( 'disabled' );
		} );

		if ( element.hasClass( 'secondary' ) ) {
			element.html( localized_data.text.mark_as_complete + '<span class="dashicons dashicons-yes onhover"></span>' );
		} else {
			element.html( localized_data.text.completed + '<span class="dashicons dashicons-no onhover"></span>' );
		}

		element.attr( 'href', originalHref );

		element.blur();

	}/* stop_loading() */


	/**
	 * Add/Change/Remove the 'you competed this x{time} ago' message.
	 * If we've just completed it, we change it to Completed just now.
	 * If we've just marked as incomplete, we remove the message.
	 *
	 * @since 1.0.0
	 *
	 * @param (object) element - a jQuery object of a HTML element
	 * @param (bool) completed - Whether we've completed or uncompleted
	 * @return null
	 */

	function change_completed_message( element, completed ) {

		if ( ! completed ) {
			element.next( '.when_completed' ).fadeOut();
		} else {
			element.after( '<span class="when_completed">' + localized_data.text.completed_just_now + '</span>' );
		}

	}/* change_completed_message() */


	/**
	 * Also update the completed count in the sidebar.
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) completed - Whether we've completed or uncompleted
	 * @return null
	 */

	function update_count_in_section_list( completed ) {

		var prevValue = get_previous_value();

		var newValue;

		// Calculate new value
		newValue = get_new_value( prevValue, completed );

		// Test if already max'd - helps prevent a quiz from looking like it's gone > 100%
		var maxCount = get_total_num_of_components();

		if ( prevValue >= maxCount ) {
			return;
		}

		// Update the value
		var updateSpan = $( '.current_page_item .completed-components-details .completed-components' );
		updateSpan.text( newValue );

	}/* update_count_in_section_list() */


	/**
	 * What's the new completed number of components. It's the old Number
	 * plus or minus 1 depending on if we've just completed or uncompleted
	 *
	 * @since 1.0.0
	 *
	 * @param (int) prevValue - The number before we did the action
	 * @param (bool) completed - Whether we've just completed or uncompleted (true = completed)
	 * @return (int) The new number of completed components
	 */

	function get_new_value( prevValue, completed ) {

		if ( completed ) {
			newValue = prevValue + 1;
		} else {
			newValue = prevValue - 1;
		}

		return newValue;

	}/* get_new_value() */

	/**
	 * Fetch the previous value of the completed components
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (int) The number of completed items before we've made any changes
	 */

	function get_previous_value() {

		// Find the completed class and the number of already completed
		var updateSpan = $( '.current_page_item .completed-components-details .completed-components' );
		var prevValue = parseInt( updateSpan.text() );

		return prevValue;

	}/* get_previous_value() */


	/**
	 * Fetch the total number of components for this section
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return (int) The total number of components
	 */

	function get_total_num_of_components() {

		var totalSpan = $( '.current_page_item .completed-components-details .total-components' );
		var totalValue = parseInt( totalSpan.text() );

		return totalValue;

	}/* get_total_num_of_components() */


	/**
	 * Update the progress bar. 2 things to Change
	 * 1) the style width
	 * 2) the text in the bubble
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) Have we completed or uncompleted?
	 * @return null
	 */

	function update_progress_bar( completed ) {

		var prevValue			= get_previous_value();

		var newValue 			= get_new_value( prevValue, completed );
		var totalNumComponents	= get_total_num_of_components();

		var new_percentage 		= get_percentage( newValue, totalNumComponents );

		var progress_div 		= $( '.current_page_item .progress' );
		var progress_span 		= $( progress_div ).find( '.meter' );
		var text_span 			= $( progress_div ).find( '.complete-percentage span' );

		// Animate the progress bar
		progress_span.animate( {
			width: new_percentage + '%'
		}, 150 );

		// Update the text
		text_span.text( new_percentage + '%' );

		//If percent is 0 add no-complete class else if above 0 remove class
		if ( new_percentage === 0 ) {
			progress_div.addClass( 'no-complete' );
		}
		else if ( new_percentage > 0 ) {
			progress_div.removeClass( 'no-complete' );
		}

		//If percent is 100 add completed class else if below 100 remove class
		if ( new_percentage === 100 ) {
			progress_div.addClass( 'completed' ).removeClass( 'start-progress' );
		}
		else if ( new_percentage < 100 ) {
			progress_div.removeClass( 'completed' );
		}

	}/* update_progress_bar() */


	/**
	 * Calculate the percentage of completed components
	 *
	 * @since 1.0.0
	 *
	 * @param (int) value - The number of completed components
	 * @param (int) total - The total number of completed components
	 * @return (int) The percentage amount of completed components
	 */

	function get_percentage( value, total ) {

		// Catch the zero case
		if ( 0 === value || 0 === total ) {
			return 0;
		}

		// Max 100%, so if for some reason value > total, return 100
		if ( value > total ) {
			return 100;
		}

		// Round to 2 d.p.
		var percentage = ( value / total ) * 100;
		percentage = +percentage.toFixed( 2 );

		return percentage;

	}/* get_percentage() */


	/**
	 * When a quiz is finished, update the count in the sidebar of completed items
	 * This is hooked into ajaxSuccess
	 *
	 * @since 1.0.0
	 *
	 * @param (Event) event -
	 * @param (jqXHR) xhr -
	 * @param (object) options -
	 * @return null
	 */

	function ajax_success__update_sidebar_completion_counts_for_quiz_completion( event, xhr, settings ) {

		if ( null === settings || false === settings || 'undefined' === settings ) {
			return;
		}

		/**
		 * OK, this is horrible. I *Really* hope there's a better way to do this
		 * that I'm not seeing yet and someone will see this, barf a little, then
		 * show me the way. However, the data sent to the AJAX request is here in
		 * settings.data. Sadly, it's a string of some sort. That looks a little
		 * like this:
		 * action=wp_pro_quiz_admin_ajax&func=quizCheckLock&data%5BquizId%5D=13
		 * Barf bag. In order to ensure that we're not running our code on every
		 * AJAX success ever, we need to parse that and ensure it's when a quiz
		 * is complete.
		 * You don't even want to see the full string for a completedQuiz action.
		 * Needless to say, it's a horror show. A disaster. An absolute nightmare.
		 * Stephen King himself couldn't have written something so terrifying.
		 * Please, sweet merciful Lord of baby hippopotamuses, let there be a
		 * better way than this. Pretty please?
		 */

		var unserializedData = settings.data;
		var iAmSoSorry = unserializedData.split( '&' );
		var iHateEverything = {};

		for ( var i = 0; i < iAmSoSorry.length; i++ ) {
			var thisShower = iAmSoSorry[ i ];
			var whyOhWhy = thisShower.split( '=' );
			var stupidkey = whyOhWhy[0];
			var stupidvalue = whyOhWhy[1];
			iHateEverything[stupidkey] = stupidvalue;
		}

		// Test that we're completing a quiz
		if ( typeof( iHateEverything.func ) === 'undefined' || 'completedQuiz' !== iHateEverything.func ) {
			return;
		}

		// And also update the completion bar
		update_progress_bar( true );

		// OK, this is a completed quiz. Increase the count.
		update_count_in_section_list( true );

	}/* ajax_success__update_sidebar_completion_counts_for_quiz_completion() */

} );
