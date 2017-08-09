/**
 * Everything about this file needs to change.
 * It's a mishmash of lots of different components and features.
 * Please make it better.
 */

( function( global ) {

	var ubc_press = function() {
		return new ubc_press.init();
	};

	var markAsCompleteButtons;
	var saveForLaterButtons;
	var localized_data = ubc_press_ajax;

	ubc_press.prototype = {

		vimeoPlayer: jQuery('.vimeo-embed'),
		vimeoPlayerOrigin: '*',
		youTubePlayers: {},

		loadYouTubeJS: function() {
			var s = document.createElement("script");
		    s.src = (location.protocol == 'https:' ? 'https' : 'http') + "://www.youtube.com/player_api";
		    var before = document.getElementsByTagName("script")[0];
		    before.parentNode.insertBefore(s, before);
		},

		addSubmitHandlerForUserNotesSave: function() {
			var userNotesForm = document.querySelector( '#ubc_user_notes_metabox' );
			if ( null === userNotesForm ) {
				return;
			}
			userNotesForm.addEventListener( 'submit', this.submit_ubc_user_notes_metabox__save_user_notes );
		},

		addClickEventHandlerForMarkAsComplete: function() {
			var mark_as_complete_buttons = this.getMarkAsCompleteButtons();
			for ( var index = 0; index < mark_as_complete_buttons.length; ++index ) {
				mark_as_complete_buttons[index].addEventListener( 'click', this.click_mark_as_complete__process_completion );
			}
		},

		addClickEventHandlerForSaveForLater: function() {
			var save_for_later_buttons = this.getSaveForLaterButtons();
			for ( var index = 0; index < save_for_later_buttons.length; ++index ) {
				save_for_later_buttons[index].addEventListener( 'click', this.click_save_for_later__process_completion );
			}
		},

		addEventHandlerForubcPressAllComponentsInSubSectionCompleted: function() {
			window.addEventListener( 'ubcPressAllComponentsInSubSectionCompleted', this.all_components_in_subsection_completed__trigger_feedback );
		},

		addEventHandlerForubcPressAllComponentsInSectionCompleted: function() {
			window.addEventListener( 'ubcPressAllComponentsInSectionCompleted', this.all_components_in_section_completed__trigger_feedback );
		},

		addAJAXSuccessHandlerForQuizCompletion: function() {
			// @TODO: Is there a event we can listen to here rather than rely on jQuery?
			jQuery( document ).ajaxSuccess( function( event, xhr, settings ) {
				ubc$.prototype.ajax_success__update_sidebar_completion_counts_for_quiz_completion( event, xhr, settings );
			} );
		},

		addAJAXSubmissionHandlerForAssignmentCompletion: function() {
			var assignmentForms = document.querySelectorAll( '.ubc-press-assignment-form' );
			var index = 0;
			for( index=0; index < assignmentForms.length; index++ ) {
				var thisForm = assignmentForms[index];
				thisForm.addEventListener( 'submit', ubc$.prototype.submit__mark_assignment_form_as_complete );
			}

		},

		addEventListenerForVimeoIFrameMessages: function() {
			window.addEventListener( 'message', this.onMessageReceived );
		},

		getMarkAsCompleteButtons: function() {

			// Check for cached buttons
			if ( typeof this.markAsCompleteButtons !== 'undefined' ) {
				return this.markAsCompleteButtons;
			}

			// None cached, so grab and cache them
			var fetched = document.querySelectorAll( '.mark-as-complete' );
			this.markAsCompleteButtons = fetched;
			return fetched;
		},

		getSaveForLaterButtons: function() {

			// Check for cached buttons
			if ( typeof this.saveForLaterButtons !== 'undefined' ) {
				return this.saveForLaterButtons;
			}

			// None cached, so grab and cache them
			var fetched = document.querySelectorAll( '.save-for-later' );
			this.saveForLaterButtons = fetched;
			return fetched;

		},


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

		click_mark_as_complete__process_completion: function( event ) {

			event.stopPropagation();
			event.preventDefault();

			var thisButton	= jQuery( this );
			var componentID = thisButton.data( 'post_id' );

			// Do our best to prevent double clicking.
			if ( thisButton.attr( 'disabled' ) !== undefined || thisButton.hasClass( 'disabled' ) ) {
				return;
			}

			// First things first; disable the buttons
			ubc$.prototype.change_all_mark_as_complete_buttons( 'disable' );

			var ajax_data = ubc$.prototype.build_data_for_ajax_complete_item( componentID );

			ubc$.prototype.ajax_complete_item( ajax_data.data, thisButton, ajax_data.originalHref );

		},/* click_mark_as_complete__process_completion() */


		click_save_for_later__process_completion: function( event ) {

			event.stopPropagation();
			event.preventDefault();

			var thisButton	= jQuery( this );
			var componentID = thisButton.data( 'post_id' );

			// Do our best to prevent double clicking.
			if ( thisButton.attr( 'disabled' ) !== undefined || thisButton.hasClass( 'disabled' ) ) {
				return;
			}

			// First things first; disable the buttons
			ubc$.prototype.change_all_save_for_later_buttons( 'disable' );

			// Collect our AJAX Data
			var ajax_data = ubc$.prototype.build_data_for_ajax_save_item( componentID );

			// Send the AJAX call
			ubc$.prototype.ajax_save_item( ajax_data.data, thisButton, ajax_data.originalHref );

		},

		all_components_in_subsection_completed__trigger_feedback: function ( event ) {
			console.log( 'All components in this sub-section are complete. Checking other sub-sections in this section.' );
		},

		all_components_in_section_completed__trigger_feedback: function ( event ) {
			ubc$.prototype.ajax_get_feedback_form( event );
		},

		ajax_get_feedback_form: function ( event ) {

			var feedbackFormAjaxURL = ubc$.prototype.getFeedbackFormAjaxURL();
			var data = { 'url': feedbackFormAjaxURL }
			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : feedbackFormAjaxURL,
				data : data,
				beforeSend: function( jqXHR, settings ) {
					console.log( 'Before get form' );
				},
				success: function( response ) {
					console.log( response );

					if ( response.success ) {

						// Grab form and inject it into html
						var formMarkup = response.data.data.form;
						jQuery( '#gravity-form-feedback' ).html( formMarkup );
						if( window['gformInitDatepicker'] ) {
							gformInitDatepicker();
						}
						jQuery( '.feedback-sec-button' ).removeClass( 'hide' );
						jQuery( '#feedback-canvas' ).foundation( 'open', false, false );

					}

					else {

						console.log( 'No feedback for you good person. Or there was no gravity form created so... no action.' );

					}

				},
				complete: function( jqXHR, textStatus ) {
					console.log( 'completed get form' );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.error( [jqXHR, textStatus, errorThrown] );
				}
			} );

		},

		getFeedbackFormAjaxURL: function () {
			return jQuery( '#feedback-canvas' ).data( 'feedbackurl' );
		},

		build_data_for_ajax_complete_item: function( component_id ) {

			var thisButton 		= jQuery( '.mark-as-complete[data-post_id="' + component_id + '"]' );
			var originalHref	= thisButton.attr( 'href' );
			var url 			= originalHref;
			var post_id 		= thisButton.data( 'post_id' );
			var nonce 			= thisButton.data( 'nonce' );

			var data = {
				post_id : post_id,
				nonce: nonce
			};

			return {
				thisButton: thisButton,
				originalHref: originalHref,
				data: data,
			};

		},

		build_data_for_ajax_save_item: function( component_id ) {

			var thisButton 		= jQuery( '.save-for-later[data-post_id="' + component_id + '"]' );
			var originalHref	= thisButton.attr( 'href' );
			var url 			= originalHref;
			var post_id 		= thisButton.data( 'post_id' );
			var nonce 			= thisButton.data( 'nonce' );

			var data = {
				post_id : post_id,
				nonce: nonce
			};

			return {
				thisButton: thisButton,
				originalHref: originalHref,
				data: data,
			};

		},

		ajax_complete_item: function( data, thisButton, originalHref ) {

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : originalHref,
				data : data,
				beforeSend: function( jqXHR, settings ) {
					ubc$.prototype.start_loading( thisButton );
				},
				success: function( response ) {

					if ( response.success ) {
						ubc$.prototype.switch_completed_state( thisButton, response.data.completed );
						ubc$.prototype.update_progress_bar( response.data.completed );
						ubc$.prototype.update_count_in_section_list( response.data.completed );
					}
				},
				complete: function( jqXHR, textStatus ) {
					ubc$.prototype.stop_loading( thisButton, originalHref );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					return;
				}
			} );

		},


		ajax_save_item: function( data, thisButton, originalHref ) {

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : originalHref,
				data : data,
				beforeSend: function( jqXHR, settings ) {
					ubc$.prototype.start_loading( thisButton );
				},
				success: function( response ) {

					if ( response.success ) {
						ubc$.prototype.switch_completed_state( thisButton, response.data.completed );
					}
				},
				complete: function( jqXHR, textStatus ) {
					ubc$.prototype.stop_loading( thisButton, originalHref );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					return;
				}
			} );

		},

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

		switch_completed_state: function( element, completed ) {

			element.toggleClass( 'success hollow' );
			element.toggleClass( 'secondary' );

		},/* this.switch_completed_state() */


		/**
		 * Show loading status after a button is pressed.
		 * Change the text
		 *
		 * @since 1.0.0
		 *
		 * @param (object) element - a jQuery object of a HTML element
		 * @return null
		 */

		start_loading: function( element ) {

			// If it's a submit button, change the text of the button
			if ( element.is( 'input[type="submit"]' ) ) {
				element.val( localized_data.text.loading );
				return;
			}

		},/* this.start_loading() */


		/**
		 * Stop the loading process
		 * Change the 'loading' text to Completed/Mark as complete, re-enable the button
		 *
		 * @since 1.0.0
		 *
		 * @param (object) element - a jQuery object of a HTML element
		 * @return null
		 */

		stop_loading: function( element, originalHref ) {

			// If this is a submit/save button, revert the text and we're done
			if ( element.is( 'input[type="submit"]' ) ) {
				element.val( localized_data.text.save );
				return;
			}

			// Urgh. Refactor! Do different things dependeing on what type of interaction
			if ( element.hasClass( 'save-for-later' ) ) {
				this.change_all_save_for_later_buttons( 'enable' );
			} else {

				this.change_all_mark_as_complete_buttons( 'enable' );

				// find the data buttons data-toggle target to change the tool tips text upon click.
				var toolTipDataToggle 	= element.attr( 'data-toggle' );
				var findToolTip 		= jQuery( '#' + toolTipDataToggle );

				if ( element.hasClass( 'secondary' ) ) {

					element.attr( 'title', localized_data.text.mark_as_complete ).html( '<span class="button-text">' + localized_data.text.mark_as_complete + '</span><svg class="ui-icon menu-icon"><use xlink:href="#checkmark-circle"></use></svg>' );
					findToolTip.html( localized_data.text.mark_as_complete );

				} else {

					element.attr( 'title', localized_data.text.completed_just_now ).html( '<span class="button-text">' + localized_data.text.completed_just_now + '</span><svg class="ui-icon menu-icon"><use xlink:href="#checkmark-circle"></use></svg>' );
					findToolTip.html( localized_data.text.completed_just_now );
				}

			}

			element.attr( 'href', originalHref );

			element.blur();

		},/* this.stop_loading() */


		/**
		 * Wrapper function to enable doing something to all buttons
		 *
		 * @since 1.0.0
		 *
		 * @param (string) status - are we enabling or disabling?
		 * @return null
		 */

		change_all_mark_as_complete_buttons: function( status ) {

			var allButtons = jQuery( 'a.mark-as-complete' );

			switch (status) {

				case 'disable':

					jQuery.each( allButtons, function( i, val ) {
						jQuery( val ).attr( 'disabled', 'disabled' );
						jQuery( val ).addClass( 'disabled' );
					} );

				break;

				case 'enable':
					jQuery.each( allButtons, function( i, val ) {
						jQuery( val ).removeAttr( 'disabled' );
						jQuery( val ).removeClass( 'disabled' );
					} );

				break;

				default:

			}

		},/* this.change_all_mark_as_complete_buttons() */


		change_all_save_for_later_buttons: function( status ) {

			var allButtons = jQuery( 'a.save-for-later' );

			switch (status) {

				case 'disable':

					jQuery.each( allButtons, function( i, val ) {
						jQuery( val ).attr( 'disabled', 'disabled' );
						jQuery( val ).addClass( 'disabled' );
					} );

				break;

				case 'enable':
					jQuery.each( allButtons, function( i, val ) {
						jQuery( val ).removeAttr( 'disabled' );
						jQuery( val ).removeClass( 'disabled' );
					} );

				break;

				default:

			}

		},

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

		// change_completed_message: function( element, completed ) {

		// 	if ( ! completed ) {
		// 		element.parents('.row').next( '.when_completed' ).fadeOut();
		// 	} else {
		// 		element.parents('.row').after( '<div class="when_completed"><span class="dashicons dashicons-clock"></span> ' + localized_data.text.completed_just_now + '</div>' );
		// 	}

		// },/* this.change_completed_message() */


		/**
		 * Also update the completed count in the sidebar.
		 *
		 * @since 1.0.0
		 *
		 * @param (bool) completed - Whether we've completed or uncompleted
		 * @return null
		 */

		update_count_in_section_list: function( completed ) {

			var prevValue = this.get_previous_value();

			var newValue;

			// Calculate new value
			newValue = this.get_new_value( prevValue, completed );

			// Test if already max'd - helps prevent a quiz from looking like it's gone > 100%
			var maxCount = this.get_total_num_of_components();

			if ( prevValue > maxCount ) {
				return;
			}

			// Update the value
			var updateSpan = jQuery( '.current-page-item .completed-components-details .completed-components' );
			updateSpan.text( newValue );

			// If this is now 100% we fire a custom action so we can ask for feedback
			if ( newValue === maxCount ) {
				var event = document.createEvent( 'Event' );
				// Define that the event name is 'ubcPressAllComponentsInSubSectionCompleted'.

				event.initEvent( 'ubcPressAllComponentsInSubSectionCompleted', true, true );
				window.dispatchEvent( event );

				// This is somewhat hacky. But probably the 'fastest'
				// It falls down if one component is included in multiple places and is completed
				// in one of them. Needs Replacing with proper AJAX calls
				this.checkIfAllSubSectionsInThisSectionAreComplete();
			}

		},/* this.update_count_in_section_list() */


		/**
		 * Without making another AJAX call, we look in the sidebar to see if all the
		 * sub-sections are at 100%. If they now are, then we fire a custom action.
		 *
		 * TODO: Replace this with an actual AJAX call to get all sub-sections completions
		 *
		 * @since 1.0.0
		 *
		 * @return null
		 */
		checkIfAllSubSectionsInThisSectionAreComplete: function() {

			// Grab all of the progress meters
			var allProgressMeters = document.getElementsByClassName( 'progress-meter-text' );

			// Default is all complete, made false if any are not 100%
			var allComplete = true;

			for ( var i = 0; i < allProgressMeters.length; ++i ) {
				var item = allProgressMeters[i];
				var thisItemCompleteValue = item.innerHTML;

				// If any one of them is not 100% then break
				if ( '100%' !== thisItemCompleteValue ) {
					allComplete = false;
					break;
				}
			}

			// If allComplete is still true, then fire a custom action to say all
			// subsesctions in this section are complete
			if ( true === allComplete ) {
				var event = document.createEvent( 'Event' );
				// Define that the event name is 'ubcPressAllComponentsInSectionCompleted'.

				event.initEvent( 'ubcPressAllComponentsInSectionCompleted', true, true );
				window.dispatchEvent( event );
			}



		},

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

		get_new_value: function( prevValue, completed ) {

			if ( completed ) {
				newValue = prevValue + 1;
			} else {
				newValue = prevValue - 1;
			}

			return newValue;

		},/* this.get_new_value() */

		/**
		 * Fetch the previous value of the completed components
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return (int) The number of completed items before we've made any changes
		 */

		get_previous_value: function() {

			// Find the completed class and the number of already completed
			var updateSpan = jQuery( '.current-page-item .completed-components-details .completed-components' );
			var prevValue = parseInt( updateSpan.text() );

			return prevValue;

		},/* this.get_previous_value() */


		/**
		 * Fetch the total number of components for this section
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return (int) The total number of components
		 */

		get_total_num_of_components: function() {

			var totalSpan = jQuery( '.current-page-item .completed-components-details .total-components' );
			var totalValue = parseInt( totalSpan.text() );

			return totalValue;

		},/* this.get_total_num_of_components() */


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

		update_progress_bar: function( completed ) {

			var prevValue			= this.get_previous_value();

			var newValue 			= this.get_new_value( prevValue, completed );
			var totalNumComponents	= this.get_total_num_of_components();

			var new_percentage 			= this.get_percentage( newValue, totalNumComponents );

			var progress_div 			= jQuery( '.current-page-item .progress' );
			var progress_span 		= jQuery( progress_div ).find( '.progress-meter' );
			var text_span 				= jQuery( progress_div ).find( '.progress-meter-text' );

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

		},/* this.update_progress_bar() */


		/**
		 * Calculate the percentage of completed components
		 *
		 * @since 1.0.0
		 *
		 * @param (int) value - The number of completed components
		 * @param (int) total - The total number of completed components
		 * @return (int) The percentage amount of completed components
		 */

		get_percentage: function( value, total ) {

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

		},/* this.get_percentage() */

		/**
		 * When a gravity form assignment form is submitted, we should mark the component
		 * as complete
		 *
		 * @since 1.0.0
		 *
		 * @param (object) event - The js event (submit event)
		 * @return null
		 */

		submit__mark_assignment_form_as_complete: function( event ) {

			mark_as_complete_button = ubc_press.prototype.findMarkAsCompleteForAssignment( event.target );
			var component_id = jQuery( mark_as_complete_button ).data( 'post_id' );

			// If it's not already complete, click it
			if ( ! jQuery( mark_as_complete_button ).hasClass( 'secondary' ) ) {
				return;
			}

			var ajax_data = ubc_press.prototype.build_data_for_ajax_complete_item( component_id );
			ubc_press.prototype.ajax_complete_item( ajax_data.data, ajax_data.thisButton, ajax_data.originalHref );

		},

		findMarkAsCompleteForAssignment: function( assignmentForm ) {
			var parent_panel = jQuery( assignmentForm ).parents( '.so-panel' );
			var mark_as_complete_button = parent_panel.find( 'a.mark-as-complete' );

			return mark_as_complete_button;
		},

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

		ajax_success__update_sidebar_completion_counts_for_quiz_completion: function( event, xhr, settings ) {

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
			this.update_progress_bar( true );

			// OK, this is a completed quiz. Increase the count.
			this.update_count_in_section_list( true );

		},/* ajax_success__update_sidebar_completion_counts_for_quiz_completion() */


		/**
		 * When the user notes form is submitted, save the notes via AJAX
		 *
		 * @since 1.0.0
		 *
		 * @param (object) event - The submit event from jQuery
		 * @return null
		 */

		submit_ubc_user_notes_metabox__save_user_notes: function( event ) {

			// Stop the normal PHP processing
			event.stopPropagation();
			event.preventDefault();

			// Grab some variables including the submitted content
			var thisForm			= jQuery( this );
			var thisButton 			= thisForm.find( 'input[type="submit"]' ).eq(0);
			var notesContentField	= jQuery( '#ubc_user_notes_content_ifr' );
			var notesContent 		= notesContentField.contents().find( 'body' ).html();

			// Do our best to prevent double clicking.
			if ( thisButton.attr( 'disabled' ) !== undefined || thisButton.hasClass( 'disabled' ) ) {
				return;
			}

			// Post ID (subsection ID) is stored in a hidden field with a name of 'object_id'
			var post_id = thisForm.find( 'input[name="object_id"]' ).eq(0).val();
			var nonce 	= thisForm.find( '#nonce_CMB2phpubc_user_notes_metabox' ).eq(0).val();

			// AJAX url
			var url		= thisForm.find( '#user_notes_ajax_url' ).eq(0).val();

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : url,
				data : {
					post_id : post_id,
					nonce: nonce,
					notes_content: notesContent
				},
				beforeSend: function( jqXHR, settings ) {
					ubc_press.prototype.start_loading( thisButton );
				},
				success: function( response ) {
					if ( response.success ) {
						ubc_press.prototype.show_saved_message( thisButton, 3000 );
					}
				},
				complete: function( jqXHR, textStatus ) {
					ubc_press.prototype.stop_loading( thisButton );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					return;
				}
			} );

		},/* submit_ubc_user_notes_metabox__save_user_notes() */


		/**
		 * Output a 'saved' message for user notes which fades out
		 *
		 * @since 1.0.0
		 *
		 * @param (object) element - The element _before_ where we want to show the message
		 * @param (int) timeout - For How long should the message be shown (ms)
		 * @return null
		 */

		show_saved_message: function( element, timeout ) {

			if ( ! jQuery( '#notes_saved_msg' ).length ) {
				var markup = '<span id="notes_saved_msg" style="display: none;"> ' + localized_data.text.saved + ' </span>';
				element.after( markup );
			}

			jQuery( '#notes_saved_msg' ).fadeIn( 'fast' ).delay( timeout ).fadeOut( 'fast' );

		},/* this.show_saved_message() */








		// Handle messages received from the player
	    onMessageReceived: function( event ) {

	        // Handle messages from the vimeo player only
	        if (!(/^https?:\/\/player.vimeo.com/).test(event.origin)) {
	            return false;
	        }

	        if ( ubc_press.prototype.vimeoPlayerOrigin === '*' ) {
	            ubc_press.prototype.vimeoPlayerOrigin = event.origin;
	        }

			if ( typeof event !== 'object' ) {
				return;
			}

			if ( typeof event.data === 'undefined' ) {
				return;
			}

			if ( typeof event.data !== 'string' ) {
				return;
			}

	        var data = JSON.parse( event.data );

	        switch (data.event) {
	            case 'ready':
	                ubc_press.prototype.onReady();
	                break;

	            // case 'playProgress':
	            //     onPlayProgress(data.data);
	            //     break;
				//
	            // case 'pause':
	            //     onPause();
	            //     break;

	            case 'finish':
	                ubc_press.prototype.onFinish(data);
	                break;
	        }
	    },

	    // Helper function for sending a message to the player
	    //
	    post: function(action, value) {
	        var data = {
	          method: action
	        };

	        if (value) {
	            data.value = value;
	        }

	        var message = JSON.stringify(data);
			var index;
			for ( index = 0; index < ubc_press.prototype.vimeoPlayer.length; ++index ) {
	    		ubc_press.prototype.vimeoPlayer[index].contentWindow.postMessage( message, ubc_press.prototype.vimeoPlayerOrigin );
			}

	    },

	    onReady: function() {
	        // post('addEventListener', 'pause');
	        this.post('addEventListener', 'finish');
	        // post('addEventListener', 'playProgress');
	    },

	    onFinish: function(data) {

			// Which player just finished?
			var player_id = data.player_id;
			var event_iframe = jQuery( '#vimeo-embed-post-id-' + player_id );

			// Find the right mark as complete button
			mark_as_complete_button = this.findMarkAsCompleteForIFrame( event_iframe );

			// If it's not already complete, click it
			if ( ! jQuery( mark_as_complete_button ).hasClass( 'secondary' ) ) {
				return;
			}

			var ajax_data = this.build_data_for_ajax_complete_item( player_id );
			this.ajax_complete_item( ajax_data.data, ajax_data.thisButton, ajax_data.originalHref );
	    },

		findMarkAsCompleteForIFrame: function( iframe ) {
			// Find the right mark as complete button
			var parent_panel = jQuery( iframe ).parents( '.so-panel' );
			var mark_as_complete_button = parent_panel.find( 'a.mark-as-complete' );

			return mark_as_complete_button;
		},

		get_youtube_frame_id: function(id) {
		    var elem = document.getElementById(id);
		    if (elem) {

				if ( ( elem.src.indexOf( 'youtube' ) === -1 ) && ( elem.src.indexOf( 'youtu.be' ) === -1 ) ) {
					return null;
				}

		        if(/^iframe$/i.test(elem.tagName)) return id; //Frame, OK
		        // else: Look for frame
		        var elems = elem.getElementsByTagName("iframe");
		        if (!elems.length) return null; //No iframe found, FAILURE
		        for (var i=0; i<elems.length; i++) {
		           if (/^https?:\/\/(?:www\.)?youtube(?:-nocookie)?\.com(\/|$)/i.test(elems[i].src)) break;
		        }
		        elem = elems[i]; //The only, or the best iFrame
		        if (elem.id) return elem.id; //Existing ID, return it
		        // else: Create a new ID
		        do { //Keep postfixing `-frame` until the ID is unique
		            id += "-frame";
		        } while (document.getElementById(id));
		        elem.id = id;
		        return id;
		    }
		    // If no element, return null.
		    return null;
		},

		YT_ready: ( function() {

			var onReady_funcs = [], api_isReady = false;
		    /* @param func function     Function to execute on ready
		     * @param func Boolean      If true, all qeued functions are executed
		     * @param b_before Boolean  If true, the func will added to the first
		                                 position in the queue*/
		    return function(func, b_before) {
		        if (func === true) {
		            api_isReady = true;
		            while (onReady_funcs.length) {
		                // Removes the first func from the array, and execute func
		                onReady_funcs.shift()();
		            }
		        } else if (typeof func == "function") {
		            if (api_isReady) func();
		            else onReady_funcs[b_before?"unshift":"push"](func);
		        }
		    };

		})(),

		addEventListerForYouTubeIFrameMessages: function() {

			this.YT_ready( function() {
			    jQuery( ".flex-video > iframe[id]" ).each( function() {
			        var identifier = this.id;
			        var frameID = ubc_press.prototype.get_youtube_frame_id( identifier );
			        if ( frameID ) {
			            ubc_press.prototype.youTubePlayers[frameID] = new YT.Player( frameID, {
			                events: {
								"onStateChange": ubc_press.prototype.youTubeOnStateChange
			                }
			            });
			        }
			    } );
			} );

		},

		youTubeOnStateChange: function( event ) {

		    var newState = event.data;

			if ( 0 !== newState ) {
				return;
			}

			var event_iframe = event.target.f;
			mark_as_complete_button = ubc_press.prototype.findMarkAsCompleteForIFrame( event_iframe );
			var component_id = jQuery( mark_as_complete_button ).data( 'post_id' );

			// If it's not already complete, click it
			if ( ! jQuery( mark_as_complete_button ).hasClass( 'secondary' ) ) {
				return;
			}

			// @TODO: Trigger AJAX
			// mark_as_complete_button.click();
			var ajax_data = ubc_press.prototype.build_data_for_ajax_complete_item( component_id );
			ubc_press.prototype.ajax_complete_item( ajax_data.data, ajax_data.thisButton, ajax_data.originalHref );

		},/* youTubeOnStateChange() */

		hasAttr: function(name) {
			return this.attr(name) !== undefined;
		},

	};

	ubc_press.init = function() {
		this.loadYouTubeJS();
		this.addClickEventHandlerForMarkAsComplete();
		this.addSubmitHandlerForUserNotesSave();
		this.addAJAXSuccessHandlerForQuizCompletion();
		this.addEventListenerForVimeoIFrameMessages();
		this.addEventListerForYouTubeIFrameMessages();
		this.addAJAXSubmissionHandlerForAssignmentCompletion();
		this.addClickEventHandlerForSaveForLater();
		this.addEventHandlerForubcPressAllComponentsInSubSectionCompleted();
		this.addEventHandlerForubcPressAllComponentsInSectionCompleted();
	};

	// trick borrowed from jQuery so we don't have to use the 'new' keyword
    ubc_press.init.prototype = ubc_press.prototype;

    // attach our ubc_press to the global object, and provide a shorthand
    global.ubc_press = global.ubc$ = ubc_press;

}( window ) );

// Aaaaannnd.... go!
var ubc_press = ubc_press();

/**
 * The youTube API expects a function in the global scope. It's called
 * when the API is ready.
 *
 * @since 1.0.0
 *
 * @param null
 * @return null
 */

function onYouTubePlayerAPIReady() {
    ubc_press.YT_ready( true );
}
