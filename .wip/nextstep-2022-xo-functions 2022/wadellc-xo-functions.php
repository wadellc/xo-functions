<?php
/*
Plugin Name: NextStep Site Extensions
Description: Code added to improve user experience or performance of nextstepadventure.com
Author: David Couch of Wade, LLC
*/



// Override Product Zoom function of WooCommerce
// https://wordpress.stackexchange.com/questions/338280/how-to-get-rid-of-the-hover-zoom-in-woocommerce-single-products
$zoom_options = array (
    'url' => false,
    'callback' => false,
    'target' => false,
    'duration' => 120, // Transition in milli seconds (default is 120)
    'on' => 'mouseover', // other options: grab, click, toggle (default is mouseover)
    'touch' => true, // enables a touch fallback
    'onZoomIn' => false,
    'onZoomOut' => false,
    'magnify' => 1, // Zoom magnification: (default is 1  |  float number between 0 and 1)
);


add_filter( 'woocommerce_single_product_zoom_options', 'custom_single_product_zoom_options', 10, 3 );
function custom_single_product_zoom_options( $zoom_options ) {
    // Disable zoom magnify:
    $zoom_options['magnify'] = 0;

    return $zoom_options;
}

// Also Disable Click on Product Image
// https://community.getbeans.io/discussion/removing-the-html-image-link-from-woocommerce-single-product/
function e12_remove_product_image_link( $html, $post_id ) {
    return preg_replace( "!<(a|/a).*?>!", '', $html );
}
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'e12_remove_product_image_link', 10, 2 );


// Notify Martha of Reviews on NextStep
//
function new_comment_moderation_recipients( $emails, $comment_id ) { 
    return array( 'martha@nextstepadventure.com' );
}
add_filter( 'comment_moderation_recipients', 'new_comment_moderation_recipients', 24, 2 );
add_filter( 'comment_notification_recipients', 'new_comment_moderation_recipients', 24, 2 );
  
  

?>