<?php
/**
 * Plugin Name: Exo-Functions
 * Plugin URI: 
 * Description: Extensions that improve administrative functions as well as augmentaions for site UX.
 * Author: Wade, LLC | David W. Couch
 * Author URI: https://wadellc.co
 * Version: 0.65
 * Last Edit: 01/06/2026. Removed items now supported by ASE.
 */





/*
 *
 * Helper functions
 * Extend native WP conventions
 **********************************/

// Add page slug to Body classes

add_filter( 'body_class', 'page_slug_body_class' );

function page_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) { $classes[] = 'page-' . $post->post_name; }
    return $classes;
}



// Search Results: Replace '?s=' with 'search/[result]'
// https://www.wpbeginner.com/wp-tutorials/how-to-change-the-default-search-url-slug-in-wordpress/

add_action( 'template_redirect', 'wpb_change_search_url' );

function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
        exit();
    }
}



// Enable shortcodes for menus and widgets
// https://hoolite.be/wordpress/enable-wordpress-shortcodes-for-menu-and-widgets/

if ( ! has_filter( 'wp_nav_menu', 'do_shortcode' ) ) {
    add_filter( 'wp_nav_menu', 'shortcode_unautop' );
    add_filter( 'wp_nav_menu', 'do_shortcode', 11 );
}

if ( ! has_filter( 'widget_text', 'do_shortcode' ) ) {
    add_filter( 'widget_text', 'shortcode_unautop');
    add_filter( 'widget_text', 'do_shortcode', 11);
}



/*
 *
 * Shortcodes
 * Dates, User Names, etc.
 * - All shortcodes here for now
 * - Maybe add as includes latter
 *********************************/

// Today's Date
// Usage: [todays_date] 

add_shortcode( 'todays_date', 'current_days_date' );

function current_days_date() {
    // Format: January 1, 2021
    return date("F j, Y");
}


// Copyright
// Usage: [wllc_copyright] 

add_shortcode( 'wllc_copyright', 'current_year' );

function current_year() {
    // Format: 2025
    return '<p class="copyright has-small-font-size">&copy; '.date("Y").' UT Ventures</p>';
}


// Username
// Usage: [current_username]
add_shortcode( 'current_username', 'display_username_shortcode' );

function display_username_shortcode() {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        //return $current_user->user_login;
        return $current_user->user_firstname;
    } else {
        return 'Guest'; // Or any other message for non-logged-in users
    }
}


// Search Field (https://wp-mix.com/wordpress-shortcode-display-search-form/)
// Usage: [search_form]
// ToDo: Establish support for setting placeholders via shortcode parameters

add_shortcode('search_form', 'display_search_form');

function display_search_form() {
    $onfocus = "this.placeholder='What can we help you find?'";
    $onblur = "this.placeholder='Search...'";
    $search_form = '<form method="get" id="search-form-shortcode" action="'. esc_url(home_url('/')) .'">
        <input type="text" name="s" id="s" placeholder="Search..." onfocus="'.$onfocus.'" onblur="'.$onblur.'">
    </form>';
    return $search_form;
}

/*function my_custom_shortcode_handler( $atts, $content = null, $shortcode_tag = '' ) {
        // Define default attributes
        $atts = shortcode_atts(
            array(
                'param1' => 'default_value1',
                'param2' => 'default_value2',
            ),
            $atts,
            $shortcode_tag
        );

        // Access the parameters
        $value1 = $atts['param1'];
        $value2 = $atts['param2'];

        // Build your output using the parameter values
        $output = "Parameter 1: " . esc_html( $value1 ) . ", Parameter 2: " . esc_html( $value2 );

        // If it's an enclosing shortcode, process the content
        if ( ! is_null( $content ) ) {
            $output .= "<p>Content: " . do_shortcode( $content ) . "</p>";
        }

        return $output;
    }*/




/* 
 * 
 * Exo Styles & Scripts
 * Supporting styles and scripts
 * - Provide toggles for optional enqueing of styles and scripts.
 * REF: 
 * wp_enqueue_style( $handle, $src, $deps, $ver, $media );
 ****************************************************************/


add_action( 'wp_enqueue_scripts', 'wadellc_ext_styles_and_scripts' );

function wadellc_ext_styles_and_scripts() {
    $plugin_url = plugin_dir_url( __FILE__ );

    // Exo Styles w file mod timestamp cachebuster
    wp_enqueue_style( 'exo-style', $plugin_url . 'css/exo-styles.css', array(), filemtime( get_stylesheet_directory() . '/style.css' ), 'all' );

    // Scripts
    // wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
 
}






/*
 *
 * Plugin Mods
 * - Check that plugin is installed.
 * - Provide toggle for each function
 ************************************/

$yoast_admin_menu_to_bottom = false;


    if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && $yoast_admin_menu_to_bottom ) {

        /* Move Yoast SEO Main Menu item to the bottom. */
        function move_yoast_to_bottom() {
            return 'low';
        }
        add_filter( 'wpseo_metabox_prio', 'move_yoast_to_bottom');
    }


?>