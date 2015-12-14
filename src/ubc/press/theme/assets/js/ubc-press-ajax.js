jQuery( document ).ready( function( $ ) {

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

		event.preventDefault();
		var thisButton	= $( this );
		var url 		= thisButton.attr( 'href' );
		var post_id 	= thisButton.data( 'post_id' );
		var nonce 		= thisButton.data( 'nonce' );

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

				if( response.success ) {
					switch_completed_state( thisButton, response.data.completed );
					update_count_in_section_list( response.data.completed );
				} else {
					console.log( response );
					alert( 'Could not mark as complete. Please refresh and try again' );
				}
			},
			complete: function( jqXHR, textStatus ) {
				stop_loading( thisButton );
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

		// First things first; disable the button
		element.attr( 'disabled', 'disabled' );
		element.addClass( 'disabled' );

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

	function stop_loading( element ) {

		element.removeAttr( 'disabled' );
		element.removeClass( 'disabled' );

		if ( element.hasClass( 'secondary' ) ) {
			element.html( localized_data.text.mark_as_complete + '<span class="dashicons dashicons-yes onhover"></span>' );
		} else {
			element.html( localized_data.text.completed + '<span class="dashicons dashicons-no onhover"></span>' );
		}

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
	 * Also update the completed count in the sidebar
	 *
	 * @since 1.0.0
	 *
	 * @param (bool) completed - Whether we've completed or uncompleted
	 * @return null
	 */

	function update_count_in_section_list( completed ) {

		// Find the completed class and the number of already completed
		var updateSpan = $( '.current_page_item .completed-components-details .completed-components' );
		var prevValue = parseInt( updateSpan.text() );

		var newValue;

		// Calculate new value
		if ( completed ) {
			newValue = prevValue + 1;
		} else {
			newValue = prevValue - 1;
		}

		// Update the value
		updateSpan.text( newValue );

	}/* update_count_in_section_list() */


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

		// OK, this is a completed quiz. Increase the count.
		update_count_in_section_list( true );

	}/* ajax_success__update_sidebar_completion_counts_for_quiz_completion() */

} );
