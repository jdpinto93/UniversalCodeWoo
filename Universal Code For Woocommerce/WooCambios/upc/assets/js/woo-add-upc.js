(function(window, document, $, undefined){

	var wooupc = {};

	wooupc.init = function() {

		if( window.wooUpcVars.is_composite && window.wooUpcVars.is_composite === '1' ) {
			wooupc.compositeVariationListener();
		} else {
			wooupc.singleVariationListener();
		}

	}

	// listens for variation change, sends ID as necessary
	wooupc.singleVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown
		    if( variation.variation_id ) {
			    var id = variation.variation_id;
			    $(".hwp-upc span").text(window.wooUpcVars.variation_upcs[id]);
			}
		} );

	}

	// listens for variation change, sends ID as necessary
	wooupc.compositeVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown

		    if( variation.variation_id ) {
			    var id = variation.variation_id;

			    $(".hwp-upc span").text( window.wooUpcVars.composite_variation_upcs[id] );
			}
		} );

	}

	wooupc.init();

	window.wooUpc = wooupc;

})(window, document, jQuery);