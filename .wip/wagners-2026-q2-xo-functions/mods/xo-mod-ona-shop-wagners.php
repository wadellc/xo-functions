<?php
/**
 * Wagner's Golf 
 * Ona Theme  mods
 */

// 1. Assets Enqueue
add_action( 'wp_enqueue_scripts', function() {
    $css_url = plugins_url( 'xo-mod-ona-shop-wagners.css', __FILE__ );
    
    // Check if the environment is NOT production
    $is_not_prod = wp_get_environment_type() !== 'production';
    $version     = $is_not_prod ? time() : '';

    wp_enqueue_style( 'ona-wagners-styles', $css_url, array(), $version );
}, 100 );



// 2. Post Type Cleanup
add_action( 'init', function() {
    unregister_post_type( 'projects' );
    unregister_post_type( 'property' );
}, 100 );