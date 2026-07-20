<?php
/* 
 * Get Us PPE Data Dashboard - Summary Counters
 * Usage: [datadash-counter type=""] 
 */


//[datadash-counter type=“requests”], [datadash-counter type=“demand”], [datadash-counter type=“delivered”]
add_shortcode( 'datadash-counter', 'datadash_counter_values' );

function datadash_counter_values( $type ) {
	extract(shortcode_atts( array(
	        'type' => 'type'
	    ), $type));

	switch ($type) {
	    case 'requests': 
	        return '<div id="request-qty" class="d3viz value"></div>';
	        break;

	    case 'demand': 
	        return '<div id="demand-qty" class="d3viz value"></div>';
	        break;

	    case 'delivered': 
	        return '<div id="delivered-qty" class="d3viz value"></div>';
	        break;

	    default:
	        return 'You entered: ' . $type .'. options include; requests, demand, or delivered.';
	        break;
	}
}
?>