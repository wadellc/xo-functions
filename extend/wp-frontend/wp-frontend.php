<?php
/**
 * Extension Name: Frontend Tools
 * Description: Client-facing utilities, shortcodes, and content layout blocks.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Shortcode | Today's Date
 * Usage: [todays_date] or [todays_date format="Y-m-d"]
 * Respects WordPress Timezone settings.
 */
add_shortcode( 'todays_date', function( $atts ) {
    $pairs = shortcode_atts( [
        'format' => 'F j, Y',
    ], $atts, 'todays_date' );

    return current_time( sanitize_text_field( $pairs['format'] ) );
});


/**
 * 2: Slug to Body Class
 * Description: Appends the post/page slug to the body class for specific CSS targeting.
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
