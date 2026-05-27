<?php
/**
 * Feature: Slug to Body Class
 * Description: Appends the post/page slug to the body class for specific CSS targeting.
 * Part of: WordPress Core Utilities
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter( 'body_class', function( $classes ) {
    if ( is_singular() ) {
        $queried_object = get_queried_object();
        if ( $queried_object && isset( $queried_object->post_name ) ) {
            $classes[] = sanitize_html_class( 'page-' . $queried_object->post_name );
        }
    }
    return $classes;
});
