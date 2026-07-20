/* Utility javascript for UTRF */

	jQuery(document).ready(function($) {

	/* **** jQuery to add css classes to Inteum widget ('Web Parts'): Available Technologies **** */

	/* Using jQuery to add classes to the 'li' containing the link item based on case sensitive category names.
		Also add classes 'technology nav navbar-nav' tu parent 'ul'. */

	    jQuery( "div.cl a:contains('Agriculture')" ).closest('li').addClass( "agriculture" ).closest('ul').addClass( "technology nav navbar-nav" );
	    jQuery( "div.cl a:contains('Animal Health')" ).closest('li').addClass( "anml_health" );
	    jQuery( "div.cl a:contains('Chemistry')" ).closest('li').addClass( "chem_mat" );
	    jQuery( "div.cl a:contains('Engineering')" ).closest('li').addClass( "engineering" );
	    jQuery( "div.cl a:contains('Energy')" ).closest('li').addClass( "energy" );
	    jQuery( "div.cl a:contains('Human Health')" ).closest('li').addClass( "hmn_hlth" );
	    jQuery( "div.cl a:contains('Research Tools')" ).closest('li').addClass( "research_tools" );
	    jQuery( "div.cl a:contains('Software')" ).closest('li').addClass( "soft_copy" );

	/* Add placeholder text to Technology Search Box */
	    jQuery("#techsearchid").attr("placeholder", "Search Technologies");

	/* Replace 'Search' text on button with Font-Awesome Search Ico.  */
		jQuery("#techsearchid+input").attr("value", "");

	/* Remove styles comming from Inteum NCS entry*/
		jQuery('.c_tp_description style').remove();

	});       
			

	jQuery(document).ready(function($) {
		
		// Add Link to Express Licensing to available technologies...
    	jQuery("ul.technology.nav.navbar-nav").append('<li class="express-licensing"><a href="/industry/express-licensing/">Express Licensing</a><div style="height:0px; overflow:hidden;"></div></li>');

	});	