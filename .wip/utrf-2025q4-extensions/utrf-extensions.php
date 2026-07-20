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
// Helper Functions
function show_last_updated_shortcode() {
    $updated_date = get_the_modified_date('F j, Y');
    $updated_time = get_the_modified_date('h:i a');
    //return 'Last updated on ' . $updated_date . ' at ' . $updated_time;
	return  $updated_date;
}
add_shortcode('last_updated', 'show_last_updated_shortcode');


function move_yoast_to_bottom() {
    return 'low';
}
add_filter( 'wpseo_metabox_prio', 'move_yoast_to_bottom');

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
    //wp_enqueue_style( 'tech_publisher_cat_style', $plugin_url . 'css/tech_publisher_categories.css' );
    //wp_enqueue_script( 'tech_publisher_cat_script', $plugin_url . 'js/tech_publisher_categories.js' );

    // Tecnology Publisher Search - Search Box and Button
    //wp_enqueue_style( 'tech_publisher_search_style', $plugin_url . 'css/tech_publisher_search.css' );

    // Tecnology Publisher Latest Technologies - Sidebar Feed of Most recently Published Technologies
    //wp_enqueue_style( 'tech_publisher_latest_style', $plugin_url . 'css/tech_publisher_latest.css' );

    
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
//require_once('templates/tech_publisher_categories.php');
//require_once('templates/tech_publisher_search.php');
//require_once('templates/tech_publisher_latest.php');

// UTRF Staff Shortcode Requires PODS Plugin
require_once('templates/pods_staff_shortcode.php');

// Woo Commerce Template overides
// Todo: Move templates from wp-content/themes/[theme]/woocommerce/
// require_once('utrf-woo.php');
// 
// Disable Zoom Function on Products
function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );




/* UTRF TechPress
 * Add Thumbnail for Technologies Admin */
/* Add Featured Images to Admin Columns 
 * https://docs.metabox.io/extensions/mb-admin-columns/
 */
add_action( 'admin_init', 'prefix_add_custom_columns', 20 );
function prefix_add_custom_columns() {
    class Prefix_Custom_Admin_Columns extends \MBAC\Post {
        public function columns( $columns ) {
            $columns  = parent::columns( $columns );
            $position = 'before';
            $target   = 'title';
            $this->add( $columns, 'featured_image', 'Thumbnail', $position, $target );
            // Add more if you want
            return $columns;
        }
        public function show( $column, $post_id ) {
            switch ( $column ) {
                case 'featured_image':
                    the_post_thumbnail( [100, 100] );
                    break;
                // More columns
            }
        }
    }
    new Prefix_Custom_Admin_Columns( 'technologies', array() );
	new Prefix_Custom_Admin_Columns( 'innovator', array() );
	//new Prefix_Custom_Admin_Columns( 'technology-category', array() );
}

add_action('admin_head', 'my_column_width');
function my_column_width() {
    echo '<style type="text/css">';
    echo '.column-featured_image, .column-utrf_tech_id { width:100px !important; overflow:hidden }';
    echo '</style>';
}


// Add image sizes.
add_image_size( 'utrf-technology-thumbnail', 225, 225, TRUE );

// Add Column to Custom Taxonomy, 'technology-category'
function utrf_technology_add_dynamic_hooks() {
	$taxonomy = 'technology-category';
	add_filter( 'manage_edit-' . $taxonomy . '_columns',  'utrf_technology_taxonomy_columns' );
}
//add_action( 'admin_init', 'utrf_technology_add_dynamic_hooks' );

function utrf_technology_taxonomy_columns( $original_columns ) {
	$new_columns = $original_columns;
	array_splice( $new_columns, 1 );
	$new_columns['tech-cat-thumbnail'] = esc_html__( 'thumbnail', 'utrf_tech_cat_img' );
	return array_merge( $new_columns, $original_columns );
}

/*
 * Add support for <iframe> 11/21/2025 ~DWC - Video URLS and embeds do not work as native in TechPress */
function add_iframe_to_tinymce($initArray) {
        $initArray['extended_valid_elements'] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
        return $initArray;
    }
    add_filter('tiny_mce_before_init', 'add_iframe_to_tinymce');


?>