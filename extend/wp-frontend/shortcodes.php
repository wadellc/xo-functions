<?php
/**
 * Frontend Tools Shortcode Repository.
 *
 * Safe execution registry for client-facing template shortcodes. Includes 
 * automated localization overrides and dynamic DOM instance auto-incrementing.
 *
 * @package    XO_Functions
 * @subpackage Frontend_Tools
 * @category   Shortcodes
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.0.0
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register XO Shortcodes on Init
 */
add_action( 'init', function() {

    // [todays_date]
    if ( ! shortcode_exists( 'todays_date' ) ) {
        add_shortcode( 'todays_date', 'xo_todays_date' );
    }

    // [current_username]
    if ( ! shortcode_exists( 'current_username' ) ) {
        add_shortcode( 'current_username', 'xo_current_username' );
    }

    // [search_form]
    if ( ! shortcode_exists( 'search_form' ) ) {
        add_shortcode( 'search_form', 'xo_search_form' );
    }

});

/**
 * 1. Callback for [todays_date]
 *
 * Respects WordPress Timezone settings.
 */
function xo_todays_date( $atts ) {
    $pairs = shortcode_atts( [
        'format' => 'F j, Y',
    ], $atts, 'todays_date' );

    return current_time( sanitize_text_field( $pairs['format'] ) );
}

/**
 * 2. Callback for [current_username]
 *
 * Returns user's first name if logged in, otherwise "Guest".
 */
function xo_current_username() {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        // Fallback to display_name if first_name isn't filled out by user
        return ! empty( $current_user->user_firstname ) ? esc_html( $current_user->user_firstname ) : esc_html( $current_user->display_name );
    }
    
    return 'Guest';
}

/**
 * 3. Callback for [search_form]
 *
 * Secure, clean search markup generator featuring dynamic ID auto-incrementing.
 */
function xo_search_form() {
    // Keep track of how many instances of this shortcode run on the current page request
    static $instance = 0;
    $instance++;

    // Generate a unique ID for each render (e.g., xo-search-1, xo-search-2)
    $form_id = 'xo-search-' . $instance;

    ob_start(); 
    ?>
    <form method="get" id="<?php echo esc_attr( $form_id ); ?>" class="xo-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" class="xo-search-input" placeholder="Search..." aria-label="Search site">
    </form>
    <?php
    return ob_get_clean();
}