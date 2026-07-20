<?php
/**
 * Plugin Name: Philippe Roy Extensions
 * Plugin URI: http://wadellc.co
 * Description: Custom functions used to customize or support philippe-roy.com.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.5.0
 */



/* 
 * Today's Date Shortcode
 * Simply returns today's date 
 * Usage: [todays_date] */
add_shortcode( 'todays_date', 'current_days_date' );
function current_days_date() {
    // Format: January 1, 2021
    return date("F j, Y");
}



/* 
 * Slug to Body class 
 */
function page_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = 'page-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'page_slug_body_class' );



/**
 * Enable shortcodes for menu navigation.
 * https://hoolite.be/wordpress/enable-wordpress-shortcodes-for-menu-and-widgets/
 */
if ( ! has_filter( 'wp_nav_menu', 'do_shortcode' ) ) {
    add_filter( 'wp_nav_menu', 'shortcode_unautop' );
    add_filter( 'wp_nav_menu', 'do_shortcode', 11 );
}



/**
 * Enable shortcodes for widgets (footer, sidebar...).
 * https://hoolite.be/wordpress/enable-wordpress-shortcodes-for-menu-and-widgets/
 */
if ( ! has_filter( 'widget_text', 'do_shortcode' ) ) {
    add_filter( 'widget_text', 'shortcode_unautop');
    add_filter( 'widget_text', 'do_shortcode', 11);
}



/* Search Form Shortcode 
 * Thanks Jeff Starr
 * https://wp-mix.com/wordpress-shortcode-display-search-form/
 * TODO: Add params for placeholder(s)
 * Usage: [search_form]
 */

function display_search_form() {
    $onfocus="this.placeholder='What can we help you find?'";
    $onblur="this.placeholder='Search...'";
    $search_form = '<form method="get" id="search-form-shortcode" action="'. esc_url(home_url('/')) .'">
        <input type="text" name="s" id="s" placeholder="Search..." onfocus="'.$onfocus.'" onblur="'.$onblur.'">
    </form>';
    return $search_form;
}
add_shortcode('search_form', 'display_search_form');

/* Search Results - Replace ?s= with 'search' slug and /result
 * https://www.wpbeginner.com/wp-tutorials/how-to-change-the-default-search-url-slug-in-wordpress/
 */
function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
        exit();
    }
}
add_action( 'template_redirect', 'wpb_change_search_url' );



/* 
 * Admin Column for Thumbnails 
 */
function add_thumbnail_to_post_list_data($column,$post_id){
    switch ($column)
    {
        case 'post_thumbnail':
            echo '<a href="' . get_edit_post_link() . '">'.the_post_thumbnail( 'thumbnail' ).'</a>';
            break;
    }
}

function add_thumbnail_to_post_list( $columns ){
    $columns['post_thumbnail'] = 'Thumbnail';
    return $columns;
}

// https://developer.wordpress.org/reference/functions/add_theme_support/
if (function_exists('add_theme_support')){
    // Add to 'posts'
    add_filter( 'manage_posts_columns' , 'add_thumbnail_to_post_list' );
    add_action( 'manage_posts_custom_column' , 'add_thumbnail_to_post_list_data', 10, 2 );
 
    // Add To Pages
    add_filter( 'manage_pages_columns' , 'add_thumbnail_to_post_list' );
    add_action( 'manage_pages_custom_column' , 'add_thumbnail_to_post_list_data', 10, 2 );
}





?>