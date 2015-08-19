jQuery( document ).ready( function( $ ) {

	if ( typeof sowEmitters === 'undefined' ) {
		return;
	}

	sowEmitters.set_this_value_to_other = function( val, args ){

		var changedField = false;

		jQuery( document ).on( 'change', 'select', function( event ) {

			changedField = event.currentTarget;
			var changedFieldID = $( changedField ).attr( 'id' );

			// Find the from field value
			var fromFieldTag 	= args[0].fieldtype;
			var fromFieldSelector = args[0].selector;

			var toFieldTag		= args[1].fieldtype;
			var toFieldSelector = args[1].selector;

			// This is the field that has changed, it'll be something like
			// widget-ubc-assignment-c27-assignment_post_id
			var fromField = $( '#' + changedFieldID );

			// To get the appropriate next field, we strip off "-" and fromFieldSelector
			// then append "-" and toFieldSelector
			var strippedID = changedFieldID.replace( '-' + fromFieldSelector, '' );

			var toField = $( '#' + strippedID + '-' + toFieldSelector );

			// Original value
			var fromFieldValue;
			if ( fromFieldTag == 'select' ) {
				var selectedOptionText = $( '#' + changedFieldID + ' option:selected' ).text();
				fromFieldValue = selectedOptionText;
			} else {
				fromFieldValue = $( fromField ).val();
			}

			// Now set the toField value to the fromFieldValue
			$( toField ).val( fromFieldValue );

		} );

		if( typeof args.length === 'undefined' ) {
            args = [args];
        }

        var returnGroups = {};
        for( var i = 0; i < args.length; i++ ) {
            if( args[i] === '' ) {
                args[i] = 'default';
            }
            returnGroups[args[i]] = val;
        }

        return returnGroups;

	};

} );
