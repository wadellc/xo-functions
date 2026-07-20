<?php
/**
 * Plugin Name: Next Step Extensions
 * Plugin URI: http://wadellc.co
 * Description: Custom functions used to customize and support nextstepadventure.com.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.5.0
 */



/* 
 * Today's Date Shortcode
 * Simply returns today's date 
 * Usage: [todays_date] */
add_shortcode( 'todays_date', 'current_days_date' );
function exo_init(){
	 function current_days_date() {
	 	// Format: January 1, 2021
	 	return date("F j, Y");
	 }
}
add_action('init', 'exo_init');



/* 
 * Slug to Body class */
function page_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = 'page-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'page_slug_body_class' );




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




/* Facet WP does not play nice with other queries */

/** Make sure all queries to be used with FacetWP
 ** have 'facetwp' => true in query args, including the
 ** query args setting in a facetwp template
 **/

/*// add 'facetwp' => false anytime it is not already set
add_action( 'pre_get_posts', function( $query ) {
    if ( ! isset( $query->query_var['facetwp'] ) ) {
        $query->set( 'facetwp', false );
    }
    return $query;
});

// use 'facetwp' query arg to determine main query
add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
    if ( isset( $query->query_vars['facetwp'] ) ) {
        $is_main_query = (bool) $query->query_vars['facetwp'];
    }
    return $is_main_query;
}, 10, 2 );*/




/* 
 * Genesis Blocks Pro Plugin Overides 
 * Not installed on Next Step 2023
 * 
 */

// Remove 'Genesis Blocks Pro' Portfolio CPT ~Phil Johnston
/*function disable_gbp_portfolio_post_type() {
    remove_action( 'init', 'Genesis\PageBuilder\Portfolio\register_portfolio_post_type' );
}
add_action( 'init', 'disable_gbp_portfolio_post_type', 9 );*/




/* 
 * Genesis Block Theme Overrides + Augmentations
 * 
 */


/* Menus */
/* Register additional Menus - Locations*/
/*function exo_register_nav_menu(){
        register_nav_menus( array(
            'connect_menu' => __( 'Connections Menu', 'nsa' ),
            //'auxillary_menu'  => __( 'Auxillary Menu', 'nsa' ),
            //'main_menu'  => __( 'Main Menu', 'nsa' ),
            'mobile_menu'  => __( 'Mobile Menu', 'nsa' ),
        ) );
    }

add_action( 'after_setup_theme', 'exo_register_nav_menu', 0 );*/






?>