( function( global ) {

	var ubc_press_admin_metaboxes = function() {
		return new ubc_press_admin_metaboxes.init();
	};


	ubc_press_admin_metaboxes.prototype = {

		collectedData: {},

		addClickHandlerForCreateAssignmentFormButton: function() {

			var createAssignmentButton = document.querySelector( '#create_assignment_form' );
			if ( null === createAssignmentButton ) {
				return;
			}

			createAssignmentButton.addEventListener( 'click', this.click_create_assignment_form__send_ajax );

		},

		/**
		 * If on an assignment creation screen, we only enable the publish button
		 * when an assignment _form_ has been created and assigned.
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return null
		 */

		addHandlerToPreventAssignmentPublishing: function() {

			if ( ! ubc_press_admin_metaboxes.prototype.onAssignmentScreen() ) {
				return;
			}

			var assignmentSubmitButton = jQuery( '#publish' );

			if( jQuery( '#create_assignment_form' ).is( ':visible') ) {
				ubc_press_admin_metaboxes.prototype.disableAssignmentPublishing();
			}

		},

		disableAssignmentPublishing: function() {
			var assignmentSubmitButton = jQuery( '#publish' );
			assignmentSubmitButton.attr( 'disabled', 'disabled' );
		},

		enableAssignmentPublishing: function() {
			var assignmentSubmitButton = jQuery( '#publish' );
			assignmentSubmitButton.removeAttr( 'disabled' );
		},

		/**
		 * Determine if we're on an assignment screen or not
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return (bool) True if we're adding/editing an assignment
		 */

		onAssignmentScreen: function() {

			if ( jQuery( 'body' ).hasClass( 'post-type-assignment' ) ) {
				return true;
			}

			return false;

		},

		click_create_assignment_form__send_ajax: function( event ) {

			// Stop the normal PHP processing
			event.stopPropagation();
			event.preventDefault();

			// Disable the button
			jQuery( this ).attr( 'disable', 'disable' );

			// We need the title and date/time set before we do anything
			var requiredFieldsSet = ubc_press_admin_metaboxes.prototype.checkForRequiredFields();

			if ( true !== requiredFieldsSet ) {
				// ubc_press_admin_metaboxes.prototype.outputErrors( requiredFieldsSet );
				ubc_press_admin_metaboxes.prototype.outputFieldMissingErrors( requiredFieldsSet );
				return;
			}

			// OK so we have the required fields, let's collect our data
			var data = ubc_press_admin_metaboxes.prototype.collectData();

			// The URL is a data attribute of the button
			var url = jQuery( '#create_assignment_form' ).data( 'ajax_url' );

			// Fire off the ajax request
			ubc_press_admin_metaboxes.prototype.sendAjaxToCreateForm( url, data );

		},

		/**
		 * Check for the required fields we need in order to create the form.
		 * We need a title and some times/dates. Returns true if all good.
		 * Otherwise returns an array of fields which need to be completed.
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return (true|array) True if required fields completed, an array of fields needed otherwise
		 */

		checkForRequiredFields: function() {

			var titleValue, dateValue, startTime, endTime;

			// The fields we need to be non-empty
			var requiredFields = {
				titleField:		jQuery( '#titlewrap #title' ),
				dateField:		jQuery( '#ubc_assignment_item_date_item_date' ),
				dateFieldEnd:	jQuery( '#ubc_assignment_item_date_item_date_closing' ),
				startTimeField:	jQuery( '#ubc_assignment_item_date_item_time_start' ),
				endTimeField:	jQuery( '#ubc_assignment_item_date_item_time_end' ),
			};

			// This will be a map of fields that are empty
			var emptyFields = [];

			// Loop over all of our fields and add to the array if it's empty
			var key, obj, prop, owns = Object.prototype.hasOwnProperty;

			for ( key in requiredFields ) {

			    if ( owns.call( requiredFields, key ) ) {

			        obj = requiredFields[key];
					var thisFieldVal = obj.val();

					this.collectedData[key] = thisFieldVal;

					if ( '' === thisFieldVal ) {
						emptyFields.push( obj[0] );
					}

			    }
			}

			if ( 0 === emptyFields.length ) {
				jQuery( '.ubc-press-caf-error' ).hide();
				return true;
			}

			return emptyFields;

		},

		/**
		 * Output errors based on which fields are missing
		 *
		 * @since 1.0.0
		 *
		 * @param (string) message - The error message to show
		 * @return null
		 */

		outputErrors: function( message ) {

			var errorsContainer = jQuery( '#create_assignment_form' ).parent();

			jQuery( errorsContainer ).after( '<p class="ubc-press-caf-error" style="">' + message + '</p>' );

		},


		outputFieldMissingErrors: function( fields ) {

			var message = ubc_press_admin_metaboxes_vars.text.please_correct;

			jQuery( fields ).each( function() {
				jQuery( this ).css( 'border-color', 'red' ).addClass( 'has-ubc-press-error' );
			} );

			ubc_press_admin_metaboxes.prototype.outputErrors( message );

		},


		/**
		 * Collect the data we need which includes the title, the dates,
		 * The options chosen for the assignment form, the nonce
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return (object) The data we need for the AJAX request
		 */

		collectData: function() {

			var requiredFields = this.collectedData;
			var otherFields = {
				submissionType: jQuery( 'input[name="ubc_press_create_assignment_form_text_area_or_file_upload_or_both"]:checked' ).val(),
				postID: jQuery( '#post_ID' ).val(),
			};

			// Merge them
			var data = {};
			for ( var attrname in otherFields ) { data[attrname] = otherFields[attrname]; }
			for ( var attrname2 in requiredFields ) { data[attrname2] = requiredFields[attrname2]; }

			return data;
		},

		/**
		 * Send the AJAX and handle the results
		 *
		 * @since 1.0.0
		 *
		 * @param (string) url - The AJAX Endpoint
		 * @param (object) data - The data we send via AJAX
		 * @return null
		 */

		sendAjaxToCreateForm: function( url, data ) {

			jQuery.ajax( {
				type : 'post',
				dataType : 'json',
				url : url,
				data : data,
				beforeSend: function( jqXHR, settings ) {
				},
				success: function( response ) {

					if ( response.success ) {

						ubc_press_admin_metaboxes.prototype.replace_instructions_with_new_content( response.data.metabox_content );

						// Enable the publish button
						ubc_press_admin_metaboxes.prototype.enableAssignmentPublishing();

					} else {

						// OK the AJAX request worked, but there was an error. Probably a 'form exists' or something. Let's output the error
						var message = response.data.message;
						ubc_press_admin_metaboxes.prototype.outputErrors( message );

					}

					ubc_press_admin_metaboxes.prototype.clearErrors();
				},
				complete: function( jqXHR, textStatus ) {
				},
				error: function( jqXHR, textStatus, errorThrown ) {
				}
			} );

		},

		/**
		 * When the 'Create Assignment Form' button is pressed and a form
		 * is susccessfully created, we replace the content of the form
		 * creation stuff with other details.
		 *
		 * @since 1.0.0
		 *
		 * @param (string) content - The content which we replacing the existing with
		 * @return null
		 */

		replace_instructions_with_new_content: function( content ) {

			var original = jQuery( '#cmb2-metabox-ubc_press_create_assignment_form_metabox' );

			original.fadeOut( 100, function() {
				original.html( content );
			} );

			original.fadeIn();
			jQuery( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );

		},/* replace_instructions_with_new_content() */

		/**
		 * Clear any errors shown
		 *
		 * @since 1.0.0
		 *
		 * @param null
		 * @return null
		 */

		clearErrors: function() {
			jQuery( '.has-ubc-press-error' ).css( 'border-color', 'auto' );
		},/* clearErrors() */

	};

	ubc_press_admin_metaboxes.init = function() {
		this.addClickHandlerForCreateAssignmentFormButton();
		this.addHandlerToPreventAssignmentPublishing();
	};

	// trick borrowed from jQuery so we don't have to use the 'new' keyword
    ubc_press_admin_metaboxes.init.prototype = ubc_press_admin_metaboxes.prototype;

    // attach our ubc_press_admin_metaboxes to the global object, and provide a shorthand
    global.ubc_press_admin_metaboxes = ubc_press_admin_metaboxes;

}( window ) );

// Aaaaannnd.... go!
jQuery(document).ready(function($) {

	// Dashicons picker
	!function(a){a.fn.dashiconsPicker=function(){var t=["menu","admin-site","dashboard","admin-media","admin-page","admin-comments","admin-appearance","admin-plugins","admin-users","admin-tools","admin-settings","admin-network","admin-generic","admin-home","admin-collapse","filter","admin-customizer","admin-multisite","admin-links","format-links","admin-post","format-standard","format-image","format-gallery","format-audio","format-video","format-chat","format-status","format-aside","format-quote","welcome-write-blog","welcome-edit-page","welcome-add-page","welcome-view-site","welcome-widgets-menus","welcome-comments","welcome-learn-more","image-crop","image-rotate","image-rotate-left","image-rotate-right","image-flip-vertical","image-flip-horizontal","image-filter","undo","redo","editor-bold","editor-italic","editor-ul","editor-ol","editor-quote","editor-alignleft","editor-aligncenter","editor-alignright","editor-insertmore","editor-spellcheck","editor-distractionfree","editor-expand","editor-contract","editor-kitchensink","editor-underline","editor-justify","editor-textcolor","editor-paste-word","editor-paste-text","editor-removeformatting","editor-video","editor-customchar","editor-outdent","editor-indent","editor-help","editor-strikethrough","editor-unlink","editor-rtl","editor-break","editor-code","editor-paragraph","editor-table","align-left","align-right","align-center","align-none","lock","unlock","calendar","calendar-alt","visibility","hidden","post-status","edit","post-trash","trash","sticky","external","arrow-up","arrow-down","arrow-left","arrow-right","arrow-up-alt","arrow-down-alt","arrow-left-alt","arrow-right-alt","arrow-up-alt2","arrow-down-alt2","arrow-left-alt2","arrow-right-alt2","leftright","sort","randomize","list-view","excerpt-view","grid-view","hammer","art","migrate","performance","universal-access","universal-access-alt","tickets","nametag","clipboard","heart","megaphone","schedule","wordpress","wordpress-alt","pressthis","update","screenoptions","cart","feedback","cloud","translation","tag","category","archive","tagcloud","text","media-archive","media-audio","media-code","media-default","media-document","media-interactive","media-spreadsheet","media-text","media-video","playlist-audio","playlist-video","controls-play","controls-pause","controls-forward","controls-skipforward","controls-back","controls-skipback","controls-repeat","controls-volumeon","controls-volumeoff","yes","no","no-alt","plus","plus-alt","plus-alt2","minus","dismiss","marker","star-filled","star-half","star-empty","flag","info","warning","share","share1","share-alt","share-alt2","twitter","rss","email","email-alt","facebook","facebook-alt","networking","googleplus","location","location-alt","camera","images-alt","images-alt2","video-alt","video-alt2","video-alt3","vault","shield","shield-alt","sos","search","slides","analytics","chart-pie","chart-bar","chart-line","chart-area","groups","businessman","id","id-alt","products","awards","forms","testimonial","portfolio","book","book-alt","download","upload","backup","clock","lightbulb","microphone","desktop","tablet","smartphone","phone","smiley","index-card","carrot","building","store","album","palmtree","tickets-alt","money","thumbs-up","thumbs-down","layout","","",""];return this.each(function(){function e(e){var o=a(e.data("target")),r=a('<div class="dashicon-picker-container"> 						<div class="dashicon-picker-control" /> 						<ul class="dashicon-picker-list" /> 					</div>').css({top:e.offset().top,left:e.offset().left}),n=r.find(".dashicon-picker-list");for(var s in t)n.append('<li data-icon="'+t[s]+'"><a href="#" title="'+t[s]+'"><span class="dashicons dashicons-'+t[s]+'"></span></a></li>');a("a",n).click(function(t){t.preventDefault();var e=a(this).attr("title");o.val("dashicons-"+e),i()});var l=r.find(".dashicon-picker-control");l.html('<a data-direction="back" href="#"> 					<span class="dashicons dashicons-arrow-left-alt2"></span></a> 					<input type="text" class="" placeholder="Search" /> 					<a data-direction="forward" href="#"><span class="dashicons dashicons-arrow-right-alt2"></span></a>'),a("a",l).click(function(e){e.preventDefault(),"back"===a(this).data("direction")?a("li:gt("+(t.length-26)+")",n).prependTo(n):a("li:lt(25)",n).appendTo(n)}),r.appendTo("body").show(),a("input",l).on("keyup",function(t){var e=a(this).val();""===e?a("li:lt(25)",n).show():a("li",n).each(function(){-1!==a(this).data("icon").toLowerCase().indexOf(e.toLowerCase())?a(this).show():a(this).hide()})}),a(document).bind("mouseup.dashicons-picker",function(a){r.is(a.target)||0!==r.has(a.target).length||i()})}function i(){a(".dashicon-picker-container").remove(),a(document).unbind(".dashicons-picker")}var o=a(this);o.on("click.dashiconsPicker",function(){e(o)})})},a(function(){a(".dashicons-picker").dashiconsPicker()})}(jQuery);

    var ubc_press_admin_metaboxes = window.ubc_press_admin_metaboxes();
});
