(function(window, document, $, undefined){

	var wooean = {};

	wooean.init = function() {

		if( window.wooEanVars.is_composite && window.wooEanVars.is_composite === '1' ) {
			wooean.compositeVariationListener();
		} else {
			wooean.singleVariationListener();
		}

	}

	// listens for variation change, sends ID as necessary
	wooean.singleVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown
		    if( variation.variation_id ) {
			    var id = variation.variation_id;
			    $(".hwp-ean span").text(window.wooEanVars.variation_eans[id]);
			}
		} );

	}

	// listens for variation change, sends ID as necessary
	wooean.compositeVariationListener = function() {

		$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		    // Fired when the user selects all the required dropdowns / attributes
		    // and a final variation is selected / shown

		    if( variation.variation_id ) {
			    var id = variation.variation_id;

			    $(".hwp-ean span").text( window.wooEanVars.composite_variation_eans[id] );
			}
		} );

	}

	wooean.init();

	window.wooEan = wooean;

})(window, document, jQuery);