( function( global ) {

	var ubc_press_dashboard = function() {
		return new ubc_press_dashboard.init();
	};


	ubc_press_dashboard.prototype = {

		/**
		 * Bind click handler for the 'View Assignment' links in the dashboard
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return null
		 */

		addClickHandlerForViewSubmissions: function() {

			var viewSubmissionsLinks = document.getElementsByClassName( 'ubc-press-view-submissions' );
			if ( null === viewSubmissionsLinks || 0 === viewSubmissionsLinks.length ) {
				return;
			}

			var i;
			for ( i = 0; i < viewSubmissionsLinks.length; i++ ) {
			    viewSubmissionsLinks[i].addEventListener( 'click', this.click_view_submissions_link__send_ajax );
			}

		},

		/**
		 * Bind click handler for the 'Sync Student List' in the dashboard
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return null
		 */

		addClickHandlerForSyncStudentList: function() {

			var syncStudentListButtons = document.getElementsByClassName( 'sync-students-list' );
			if ( null === syncStudentListButtons || 0 === syncStudentListButtons.length ) {
				return;
			}

			var i;
			for ( i = 0; i < syncStudentListButtons.length; i++ ) {
			    syncStudentListButtons[i].addEventListener( 'click', this.click_sync_student_list_button__send_ajax );
			}

		},

		click_sync_student_list_button__send_ajax: function( event ) {

			// Stop the normal PHP processing
			event.stopPropagation();
			event.preventDefault();

			var thisItem = event.target;

			var dept 	= jQuery( thisItem ).data( 'dept' );
			var course 	= jQuery( thisItem ).data( 'course' );
			var section	= jQuery( thisItem ).data( 'section' );
			var year 	= jQuery( thisItem ).data( 'year' );
			var session = jQuery( thisItem ).data( 'session' );
			var campus 	= jQuery( thisItem ).data( 'campus' );

			var url 	= jQuery( thisItem ).data( 'ajax_url' );

			var data = {
				dept : dept,
				course : course,
				section : section,
				year : year,
				session : session,
				campus : campus,
			}

			// Change button value from "Sync Student List" to "Synching Students"
			jQuery( thisItem ).val( 'Synching Students' );
			// Disable
			jQuery( thisItem ).attr( 'disabled', 'disabled' );

			ubc_press_dashboard.prototype.syncStudentListForCourse( data, url, thisItem );

		},/* click_sync_student_list_button__send_ajax() */

		/**
		 * The click handler for when someone clicks on a 'View Assignments' link. We
		 * grab the post ID and then fire an AJAX Request
		 *
		 * @since 1.0.0
		 *
		 * @param (object) event - The click event object
		 * @return null
		 */

		click_view_submissions_link__send_ajax: function( event ) {

			// Stop the normal PHP processing
			event.stopPropagation();
			event.preventDefault();

			var thisItem = event.target;
			var assignmentID = jQuery( thisItem ).data( 'post_id' );
			var url = jQuery( thisItem ).attr( 'href' );

			ubc_press_dashboard.prototype.showNextSpinner( thisItem );
			ubc_press_dashboard.prototype.fetchSubmissionsForAssignment( assignmentID, url, thisItem );

		},

		/**
		 * Run the AJAX call to fetch the submissions for the passed
		 * assignment ID
		 *
		 * @since 1.0.0
		 *
		 * @param (int) assignmentID - The post ID of the assignment for the submissions we're fetching
		 * @return null
		 */

		fetchSubmissionsForAssignment: function( assignmentID, url, thisItem ) {

			var data = {
				'post_id': assignmentID,
			};

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : url,
				data : data,
				beforeSend: function( jqXHR, settings ) {

				},
				success: function( response ){

					if ( response.success ) {

						// We inject a <tr> below the parent of the currently clicked link
						var newRow = ubc_press_dashboard.prototype.addTableRowForResults( thisItem );

						// Now, for each of the returned posts, we add to that <tr>
						var injectedBody = jQuery( newRow ).find( '.ubc-submissions-for-assignment' );

						injectedBody.hide();

						var submissions = response.data.submissions.submissions;

						var markupToAdd = '';
						for ( var i = 0; i < submissions.length; i++ ) {

							var thisSubmission = submissions[i];

							markupToAdd += ubc_press_dashboard.prototype.addAssignmentRowMArkup( thisSubmission );
						}

						// Now add that markup
						injectedBody.append( markupToAdd ).fadeIn();

					} else {



					}

				},
				complete: function( jqXHR, textStatus ) {
					ubc_press_dashboard.prototype.hideNextSpinner( thisItem );
				},
				error: function( jqXHR, textStatus, errorThrown ) {

				}
			} );

		},

		/**
		 * Run the AJAX call to sync the student list for a course
		 *
		 * @since 1.0.0
		 *
		 * @param (object) data - data to send, info about the course
		 * @param (string) the AJAX url
		 * @param (jQuery event target) the item that has been clicked (the button)
		 * @return null
		 */

		syncStudentListForCourse: function ( data, url, item ) {

			console.log( [data, url, item] );

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : url,
				data : data,
				beforeSend: function( jqXHR, settings ) {
					console.log( ['beforeSend', jqXHR, settings] );
				},
				success: function( response ){

					console.log( ['response', response] );
					if ( response.success ) {



					} else {



					}

				},
				complete: function( jqXHR, textStatus ) {
					console.log( 'complete' );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					// console.error( [jqXHR, textStatus, errorThrown] );
					console.log( jqXHR.responseText );
				}
			} );

		},


		/**
		 * To show our submissions under each assignment, and it not look like a
		 * bag of garbage, we need to inject a new <tr> after the assignment for
		 * which we are grabbing the assignments.
		 *
		 * The childElement we're passed is the clicked on 'View Assignments' link
		 * so we need to go up the DOM to get the parent <tr> and then inject our
		 * own <tr> after that.
		 *
		 * @since 1.0.0
		 *
		 * @param   -
		 * @return
		 */

		addTableRowForResults: function( childElement ) {

			// Find the parent row for the button just clicked
			var parentRow = ubc_press_dashboard.prototype.findParentTableRow( childElement );

			var newRowMarkup = ubc_press_dashboard.prototype.newRowMarkup();

			var newRow = jQuery( parentRow ).after( newRowMarkup );

			return jQuery( newRow ).next( 'tr' );

		},

		newRowMarkup: function() {

			var markup = '<tr class="ubc-press-injected-tr" style="background: white;"><td colspan="4" style="padding: 0;"><div class="ubc-press-assignment-submissions-wrap"><table style="width: 100%; border-color: #0072AC; border-spacing: 0; border-bottom: 1px solid #0072AC;"><thead style="background: #0072AC;"><th style="color: white;">Title</th><th style="color: white;">Link</th><th style="color: white;">Author</th><th style="color: white;">Grade</th></thead><tbody class="ubc-submissions-for-assignment"></tbody></table></div></td></tr>';

			return markup;

		},

		findParentTableRow: function ( childElement ) {
			var parentRow = jQuery( childElement ).parents( 'tr' );
			return parentRow.eq(0);
		},

		addAssignmentRowMArkup: function ( assignmentObject ) {

			var title = assignmentObject.title;
			var url = assignmentObject.url;
			var author = assignmentObject.author;
			var graded = assignmentObject.graded;

			var markup = '<tr><td class="tg-baqh" style="border: 1px solid #0071AE;border-top: 0;">' + title + '</td><td class="tg-baqh" style="border: 1px solid #0071AE;border-top: 0;border-left: 0;">' + url + '</td><td class="tg-baqh" style="border: 1px solid #0071AE;border-left: 0;border-top: 0;">' + author + '</td><td class="tg-baqh" style="border: 1px solid #0071AE;border-left: 0;border-top: 0;">' + graded + '</td></tr>';

			return markup;

		},

		/**
		 * Show the spinner next to the passed element
		 *
		 * @since 1.0.0
		 *
		 * @param (object) element - The element before the spinner we're going to activate
		 * @return null
		 */

		showNextSpinner: function( element ) {
			jQuery( element ).siblings( '.spinner' ).show().css('visibility','visible').css( 'float', 'none' );

		},

		/**
		 * Hide the spinner next to the passed element
		 *
		 * @since 1.0.0
		 *
		 * @param (object) element - The element before the spinner we're going to deactivate
		 * @return null
		 */

		hideNextSpinner: function( element ) {
			jQuery( element ).siblings( '.spinner' ).hide().css('visibility','hidden');
		},

	};

	ubc_press_dashboard.init = function() {
		this.addClickHandlerForViewSubmissions();
		this.addClickHandlerForSyncStudentList();
	};

	// trick borrowed from jQuery so we don't have to use the 'new' keyword
    ubc_press_dashboard.init.prototype = ubc_press_dashboard.prototype;

    // attach our ubc_press_dashboard to the global object, and provide a shorthand
    global.ubc_press_dashboard = ubc_press_dashboard;

}( window ) );

// Aaaaannnd.... go!
jQuery( document ).ready(function( $ ) {

    var ubc_press_dashboard = window.ubc_press_dashboard();

});
