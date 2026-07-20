/* JS supporting Media Center Shortcodes */

/* Hide Clear Filters button on click */
(function($) {

    $(document).on('facetwp-loaded', function() {
        //var qs = FWP.build_query_string(); // deprecated in FWP 3.9 Updated 11/9/2021 ~DWC
        var qs = FWP.buildQueryString();
        if ( '' === qs ) { // no facets are selected
            $('#fwclearall').hide();
            //alert('changed');
        } else {
			$('#fwclearall').show();
			//alert('changed');
        }
    });

    /* Stop-gap hidding Clear filters Button that displays on FWP.build_query_string - Load More... */
	$(document).mousemove(function(event){
		if( $('.facetwp-selections').is(':empty') ) {
			$('#fwclearall').hide();
			//alert('changed');
		}
	});

})(jQuery);