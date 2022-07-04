(function(window, document, $, undefined){

	var woompn = {};

	woompn.init = function() {

		if( window.wooMpnVars.is_composite && window.wooMpnVars.is_composite === '1' ) {
			woompn.compositeVariationListener();
		} else {
			woompn.singleVariationListener();
		}

	}

	// listens for variation change, sends ID as necessary
	woompn.singleVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown
		    if( variation.variation_id ) {
			    var id = variation.variation_id;
			    $(".hwp-mpn span").text(window.wooMpnVars.variation_mpns[id]);
			}
		} );

	}

	// listens for variation change, sends ID as necessary
	woogtin.compositeVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown

		    if( variation.variation_id ) {
			    var id = variation.variation_id;

			    $(".hwp-mpn span").text( window.wooMpnVars.composite_variation_mpns[id] );
			}
		} );

	}

	woompn.init();

	window.wooMpn = woompn;

})(window, document, jQuery);