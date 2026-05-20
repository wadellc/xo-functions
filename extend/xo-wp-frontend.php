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
 * 1. TODAY'S DATE SHORTCODE
 * Usage: [todays_date] or [todays_date format="Y-m-d"]
 * Respects WordPress Timezone settings.
 */
add_shortcode( 'todays_date', function( $atts ) {
    $pairs = shortcode_atts( [
        'format' => 'F j, Y',
    ], $atts, 'todays_date' );

    return current_time( sanitize_text_field( $pairs['format'] ) );
});
