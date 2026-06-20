<?php
/**
 * Frontend Tools Core Filtering Actions.
 *
 * Implements custom theme overrides including dynamic document body class additions
 * and multi-pass shortcode evaluation hooks inside navigation templates and legacy sidebars.
 *
 * @package    XO_Functions
 * @subpackage Frontend_Tools
 * @category   Theme_Filters
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.0.0
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Slug to Body Class
 * // TODO: Update static 'page-' [post_type-] prefix to be dynamic based on queried object type (post, page, product, etc.)
 * Appends the post/page slug to the body class for specific CSS targeting.
 */
add_filter( 'body_class', function( $classes ) {
    if ( is_singular() ) {
        $queried_object = get_queried_object();
        if ( $queried_object && isset( $queried_object->post_name ) ) {
            $classes[] = sanitize_html_class( 'page-' . $queried_object->post_name );
        }
    }
    return $classes;
});

/**
 * 2. Enable Shortcodes for Menus and Widgets
 * //ToDo:  Consider Dropping. Menus and Widgets are both moving towards block-based implementations that support native shortcode parsing. 
 *          This may be redundant and could cause conflicts with the new block-based widgets screen if left in place too long.
 * //Update: As of 2024, the classic widgets screen is still widely used and the new block-based widgets screen does not yet support shortcode
 *           parsing in all widget types. This filter remains relevant for ensuring backward compatibility and supporting sites that have not
 *           transitioned to the block-based widgets screen. Perform site audit.
 * Parses shortcodes inside navigation items and legacy widgets using modern wp_do_shortcode.
 */
add_action( 'init', function() {
    // Menu shortcode parsing
    if ( ! has_filter( 'wp_nav_menu', 'wp_do_shortcode' ) && ! has_filter( 'wp_nav_menu', 'do_shortcode' ) ) {
        add_filter( 'wp_nav_menu', 'shortcode_unautop' );
        add_filter( 'wp_nav_menu', 'wp_do_shortcode', 11 );
    }

    // Widget text shortcode parsing
    if ( ! has_filter( 'widget_text', 'wp_do_shortcode' ) && ! has_filter( 'widget_text', 'do_shortcode' ) ) {
        add_filter( 'widget_text', 'shortcode_unautop' );
        add_filter( 'widget_text', 'wp_do_shortcode', 11 );
    }
});