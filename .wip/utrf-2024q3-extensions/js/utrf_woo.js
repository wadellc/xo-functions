/* 
 * UTRF Express License Validation 
 */
	jQuery(document).ready(function($) {

		// Alert Message to show if checkboxes are not clicked.
		var reqs = "ATTENTION: The requirements for licensing have not been met.\nPlease see the Requirements for Licensing section above.";

		// Don't allow Add to Cart to work until Requirements are checked.
		jQuery('.button.single_add_to_cart_button').click(function(){
			console.log('button clicked');
		    if(jQuery('#accept').is(':checked') && jQuery('#refund').is(':checked') && jQuery('#rep').is(':checked')){
		        // do nothing
		    }else{
				alert(reqs);
				jQuery("input:checkbox:not(:checked)").parent().addClass( "alert-danger");
	        	return false;
	        }
		});


		// If the check boxes are highlighted, clear it when clicked, also add it if it gets unchecked again.
		jQuery('input:checkbox').click(function(){
		    if(jQuery(this).is(":checked")) {
		        jQuery(this).parent().removeClass( "alert-danger");
		    } else {
		        jQuery(this).parent().addClass( "alert-danger");
		    }
		});


	});