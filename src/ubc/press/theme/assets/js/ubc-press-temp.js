( function( global ) {

	var ubc_press_temp = function() {
		return new ubc_press_temp.init();
	};

	ubc_press_temp.prototype = {

	};

	ubc_press_temp.init = function() {

	};

	// trick borrowed from jQuery so we don't have to use the 'new' keyword
    ubc_press_temp.init.prototype = ubc_press_temp.prototype;

    // attach our ubc_press_temp to the global object, and provide a shorthand
    global.ubc_press_temp = ubc_press_temp;

}( window ) );

// Aaaaannnd.... go!
jQuery(document).ready(function($) {

    var ubc_press_temp = window.ubc_press_temp();

});
