<?php
/**
 * Plugin Name: UTRF Extensions
 * Plugin URI: 
 * Description: A custom plugin developed for support of custom integrations of WooCommerce and Inteum Technology Publisher.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.7
 * 
 * Todo: 
 * * 11/20/21 Woocommerce templates are still under wp-content/themes/[theme]/woocommerce. Move these into the plugin directories.
 * 
 */

// Styles & Scripts


/* 
 * Enqueue styles and supporting scripts for 
 * Inteum Technology Publisher Category Grid and Latest Technologies Feed
 */
add_action( 'wp_enqueue_scripts', 'utrf_ext_styles_and_scripts' );
function utrf_ext_styles_and_scripts() {

	// External Resources
	// wp_enqueue_script( 'utrf_fontawesome_script_sri', 'https://kit.fontawesome.com/0adbe4dfe9.js', '', null, true );
	wp_enqueue_style( 'utsystem_creative_fonts', '//cloud.typography.com/7717754/758008/css/fonts.css');

    $plugin_url = plugin_dir_url( __FILE__ );




// Technology Publisher Category Grid (JS adds classes to html from Inteum. CSS builds the grid)
    wp_enqueue_style( 'tech_publisher_cat_style', $plugin_url . 'css/tech_publisher_categories.css' );
    wp_enqueue_script( 'tech_publisher_cat_script', $plugin_url . 'js/tech_publisher_categories.js' );

    // Tecnology Publisher Search - Search Box and Button
    wp_enqueue_style( 'tech_publisher_search_style', $plugin_url . 'css/tech_publisher_search.css' );

    // Tecnology Publisher Latest Technologies - Sidebar Feed of Most recently Published Technologies
    wp_enqueue_style( 'tech_publisher_latest_style', $plugin_url . 'css/tech_publisher_latest.css' );

    
// UTRF Staff Shortcode
    wp_enqueue_style( 'utrf_staff_style', $plugin_url . 'css/pods_staff_shortcode.css' );

    
// UTRF WooCommerce Mods
    //wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
    wp_enqueue_script( 'utrf_woo_script', $plugin_url . 'js/utrf_woo.js', array( 'jquery' ), '2.0', true );
    wp_enqueue_style( 'utrf_woo_style', $plugin_url . 'css/utrf_woo.css' );



// Filters for Sub-resource Integrity attributes of styles and scripts 
    add_filter( 'style_loader_tag', 'equeued_styles_and_scripts_add_sri', 10, 2 );
    add_filter( 'script_loader_tag', 'equeued_styles_and_scripts_add_sri', 10, 2 );
 
}


    /**
    * Add SRI (Sub Resource Integrity) attributes based on defined script/style handles.
    * Thank you bink19th : https://developer.wordpress.org/reference/functions/wp_enqueue_script/#comment-4246
    */
    function equeued_styles_and_scripts_add_sri( $html, $handle ) : string {
        switch( $handle ) {

        // scripts
            case 'utrf_fontawesome_script_sri' :
                $html = str_replace( '></script>', ' crossorigin="anonymous"></script>', $html );
                break;
        } 
        return $html;
    }



/* Templates */

// Tech Publisher Category Grid Template and Shortcodes
require_once('templates/tech_publisher_categories.php');
require_once('templates/tech_publisher_search.php');
require_once('templates/tech_publisher_latest.php');

// UTRF Staff Shortcode Requires PODS Plugin
require_once('templates/pods_staff_shortcode.php');

// Woo Commerce Template overides
// Todo: Move templates from wp-content/themes/[theme]/woocommerce/
// require_once('utrf-woo.php');

?>