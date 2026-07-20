<?php
/**
 * Plugin Name: Get Us PPE Extensions
 * Plugin URI: 
 * Description: Adds the Custom Post Type - Media Center and supporting Taxonomies. Featured media has a dependency using scripts from Swiper, an open source javaScript slider library. News archives are rendered with the use of FacetWP. Featured News has it's own shortcode. News Archive is implemented with a series of FacetWP Shortcodes. It also requires a function modifying all WordPress queries in the plugin GUPPE Functions. GUPPE Extensions now also includes support for 7 new modules on the Data Dashboard. Dependencies include D3, Bootstrap, Font Awesome and Bootstrap Popper libraries. 
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.1.0
 */




// Styles & Scripts

/* 
 * Enqueue styles and supporting scripts for 
 * GUPPE Data Dashboard - only on 'data-dashboard'
 */
add_action( 'wp_enqueue_scripts', 'guppe_datadash_styles_and_scripts' );
function guppe_datadash_styles_and_scripts() {

    $plugin_url = plugin_dir_url( __FILE__ );

// Data Dashboard
    // Shared Styles and scripts for all Dashboard components

    // To minimize Bootstrap impact on site we'll add it directly to the Data Dash page editor.
    //wp_enqueue_style( 'guppe_bootstrap_style_sri', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' );

    wp_enqueue_style( 'guppe_fontawesome_style_sri', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css' );
    wp_enqueue_style( 'guppe_datadash-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap' );

    
    wp_enqueue_script( 'guppe_datadash_popper_js_sri', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', '', null, true );
    wp_enqueue_script( 'guppe_datadash_bootstrap_js_sri', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', 'guppe_datadash_popper_js_sri', null, true );
    wp_enqueue_script( 'guppe_datadash_d3v4', 'https://d3js.org/d3.v4.js');


        // 1. PPE Supply Remaining
            wp_enqueue_script( 'guppe_datadash_psr_main', $plugin_url . 'js/guppe-datadash-ppe-supply-remaining.js', '', null, true );
            wp_enqueue_style( 'guppe_datadash_psr_css', $plugin_url . 'css/guppe-datadash-ppe-supply-remaining.css' );

        
        // 2. PPE Requests by State
            wp_enqueue_style( 'guppe_datadash_rbs_css', $plugin_url . 'css/guppe-datadash-requests-by-state.css' );

            // Scripts Require D3v4 loaded above in Data Dashboard shared scripts.
                wp_enqueue_script( 'guppe_datadash_d3_chromatic', 'https://d3js.org/d3-scale-chromatic.v1.min.js', 'guppe_datadash_d3v4', null, true );
                wp_enqueue_script( 'guppe_datadash_d3_geo', 'https://d3js.org/d3-geo-projection.v2.min.js', 'guppe_datadash_d3v4', null, true );
                wp_enqueue_script( 'guppe_datadash_rbs_main', $plugin_url . 'js/guppe-datadash-requests-by-state.js', 'guppe_datadash_d3v4', null, true );

        
        // 3. Hospital Request vs. Non-hospital Requests
            wp_enqueue_script( 'guppe_datadash_hvn_main', $plugin_url . 'js/guppe-datadash-hospital-vs-non-requesting-ppe.js', 'guppe_datadash_d3v4', null, true );
            wp_enqueue_style( 'guppe_datadash_hvn_css', $plugin_url . 'css/guppe-datadash-hospital-vs-non-requesting-ppe.css' );


        // 4. Most Requested Types of PPE
            wp_enqueue_script( 'guppe_datadash_mrq_main', $plugin_url . 'js/guppe-datadash-most-requested-ppe-types.js', '', null, true );
            wp_enqueue_style( 'guppe_datadash_mrq_css', $plugin_url . 'css/guppe-datadash-most-requested-ppe-types.css' );

        // 5. PPE Requests by Category
            wp_enqueue_script( 'guppe_datadash_d3_amcharts_core', 'https://www.amcharts.com/lib/4/core.js', '', null, true );
            wp_enqueue_script( 'guppe_datadash_d3_amcharts_charts', 'https://www.amcharts.com/lib/4/charts.js', 'guppe_datadash_d3_amcharts_core', null, true );
            wp_enqueue_script( 'guppe_datadash_rbc_main', $plugin_url . 'js/guppe-datadash-ppe-requests-by-category.js', 
                array('guppe_datadash_popper_js_sri', 'guppe_datadash_bootstrap_js_sri', 'guppe_datadash_d3_amcharts_charts'), null, true );
            
            wp_enqueue_style( 'guppe_datadash_rbc_css', $plugin_url . 'css/guppe-datadash-ppe-requests-by-category.css' );


        // 6. Delivered vs. Requested
            wp_enqueue_script( 'guppe_datadash_dvr_main', $plugin_url . 'js/guppe-datadash-ppe-delivered-vs-requested.js', array('guppe_datadash_popper_js_sri', 'guppe_datadash_bootstrap_js_sri'), null, true );
            wp_enqueue_style( 'guppe_datadash_dvr_css', $plugin_url . 'css/guppe-datadash-ppe-delivered-vs-requested.css' );

        // 7. Summary Counters
            wp_enqueue_script( 'guppe_datadash_summary_counters_main', $plugin_url . 'js/guppe-datadash-summary-counters.js', '', null, true );
            wp_enqueue_style( 'guppe_datadash_summary_counters_css', $plugin_url . 'css/guppe-datadash-summary-counters.css' );

        // 8. PPE Deliveries by State
            wp_enqueue_style( 'guppe_datadash_dbs_css', $plugin_url . 'css/guppe-datadash-deliveries-by-state.css' );

            // Scripts Require D3v4 loaded above in Data Dashboard shared scripts.
                wp_enqueue_script( 'guppe_datadash_dbs_main', $plugin_url . 'js/guppe-datadash-deliveries-by-state.js', 'guppe_datadash_d3v4', null, true );


        // Globals
            // Load js after last viz
            wp_enqueue_script( 'guppe_datadash_script', $plugin_url . 'js/guppe-datadash.js', 'guppe_datadash_dvr_main', null, true );
            //wp_enqueue_style( 'guppe_datadash_style_overrides', $plugin_url . 'css/guppe-datadash.css' );



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
        case 'guppe_datadash_popper_js_sri' :
            $html = str_replace( '></script>', ' integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>', $html );
            break;
        case 'guppe_datadash_bootstrap_js_sri' :
            $html = str_replace( '></script>', ' integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>', $html );
            break;

    //styles
        case 'guppe_bootstrap_style_sri' :
            $html = str_replace( ' />', ' integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />', $html );
            break;
        case 'guppe_fontawesome_style_sri' :
            $html = str_replace( ' />', ' integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />', $html );
            break;
    } 
    return $html;
}







/* 
 * Enqueue styles and supporting scripts for 
 * GUPPE Media Center &  Spotlight Module.
 */
add_action( 'wp_enqueue_scripts', 'guppe_ext_styles_and_scripts' );
function guppe_ext_styles_and_scripts() {

    $plugin_url = plugin_dir_url( __FILE__ );

    // Media Center
    wp_enqueue_style( 'guppe_ext-styles', $plugin_url . 'css/guppe-media_center.css' );
    wp_enqueue_script( 'guppe_ext-js', $plugin_url . 'js/guppe-media_center.js' );


    // Spotlight Module
    wp_enqueue_style( 'guppe_spotlight-styles', $plugin_url . 'css/guppe-spotlight.css' );
    wp_enqueue_script( 'guppe_spotlight-js', 'https://unpkg.com/swiper/swiper-bundle.min.js' );  
}








// Media Center Custom Post Type Definitions
require_once('guppe-cpt_media-center.php');

    // Media Center Shortcode for Featured Media
    require_once('templates/guppe-cpt_media_sc-featured.php');

    // Media Center Shortcode for Archive Media
    require_once('templates/guppe-cpt_media_sc-archive.php');



// Spotlight Custom Post Type Definitions
require_once('guppe-cpt_spotlight.php');

    // Media Center Shortcode for Featured Media
    require_once('templates/guppe-cpt_spotlight-module.php');



// Data Dashboard
    // 1 PPE Supply Remaining
    require_once('templates/guppe-datadash-ppe-supply-remaining.php'); 

    // 2 PPE Request by State
    require_once('templates/guppe-datadash-ppe-requests-by-state.php'); 

    // 3 PPE Request Hospital vs Non
    require_once('templates/guppe-datadash-hospital-vs-non-requesting-ppe.php'); 

    // 4 Most Requested Types
    require_once('templates/guppe-datadash-most-requested-ppe-types.php'); 

    // 5 Requests by Category
    require_once('templates/guppe-datadash-ppe-requests-by-category.php'); 

    // 6 Delivered vs. Requested
    require_once('templates/guppe-datadash-ppe-delivered-vs-requested.php'); 

    // 7 Summary Counters
    require_once('templates/guppe-datadash-summary-counters.php'); 

    // 8 PPE Deliveries by State
    require_once('templates/guppe-datadash-ppe-deliveries-by-state.php'); 


?>