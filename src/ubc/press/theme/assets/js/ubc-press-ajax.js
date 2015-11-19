jQuery( document ).ready( function( $ ) {

	var localized_data = ubc_press_ajax;

	$( 'body' ).on( 'click', '.mark-as-complete', click_mark_as_complete__process_completion );

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
				} else {
					console.log( 'bums' );
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
			element.text( localized_data.text.mark_as_complete );
		} else {
			element.text( localized_data.text.completed );
		}

	}/* stop_loading() */


	/**
	 * Add/Change/Remove the 'you competed this x{time} ago' message.
	 * If we've just completed it, we change it to Completed just now.
	 * If we've just marked as incomplete, we remove the message.
	 *
	 * @since 1.0.0
	 *
	 * @param   -
	 * @return
	 */

	function change_completed_message( element, completed ) {

		if ( ! completed ) {
			element.next( '.when_completed' ).fadeOut();
		} else {
			element.after( '<span class="when_completed">' + localized_data.text.completed_just_now + '</span>' );
		}

	}/* change_completed_message() */

} );
