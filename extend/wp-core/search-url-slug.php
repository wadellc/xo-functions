<?php
/**
 * Pretty Search URL Slug Rewriting.
 *
 * Redirects the default ?s= search query string to a clean /search/{term}/
 * URL. Originally sourced from a WPBeginner tutorial as a bare redirect with
 * no matching rewrite rule, which sent visitors to a 404 — this version adds
 * the missing rewrite rule (self-flushed once, so no manual Permalinks visit
 * is required) and fixes the resulting redirect-loop risk that introducing
 * that rewrite rule creates if the redirect isn't scoped correctly (see
 * xo_redirect_search_url() below).
 *
 * @package    XO_Functions
 * @subpackage Core_Utilities
 * @category   Frontend_URLs
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.8
 * @since      2.1.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers the rewrite rule mapping /search/{term}/ back to the internal
 * ?s={term} query — without this, the redirect below has nowhere valid to
 * land and every search 404s.
 */
function xo_register_search_url_rewrite() {
    add_rewrite_rule( '^search/(.+)/?$', 'index.php?s=$matches[1]', 'top' );
}
add_action( 'init', 'xo_register_search_url_rewrite' );

/**
 * Redirects the classic ?s= search URL to the clean /search/{term}/ slug.
 *
 * Only fires for requests that arrive with a literal ?s= query string
 * ( isset( $_GET['s'] ) ) — NOT merely `is_search()` — because once the
 * rewrite rule above is registered, a visit to the already-clean
 * /search/{term}/ URL also satisfies is_search() with a populated query var,
 * but never sets $_GET['s'] (the term arrives via the rewrite match, not a
 * literal query string). Checking is_search() alone here would redirect the
 * clean URL to itself: an infinite loop.
 */
function xo_redirect_search_url() {
    if ( ! is_search() || ! isset( $_GET['s'] ) ) {
        return;
    }

    $search_term = get_query_var( 's' );

    if ( '' === $search_term ) {
        return;
    }

    wp_safe_redirect( home_url( '/search/' . rawurlencode( $search_term ) . '/' ) );
    exit;
}
add_action( 'template_redirect', 'xo_redirect_search_url' );

/**
 * Flushes rewrite rules once after this module first has the new rule
 * registered, so it takes effect immediately rather than requiring a manual
 * visit to Settings > Permalinks. Guarded by an option flag so the
 * (relatively expensive) flush only ever runs once, not on every request.
 */
function xo_maybe_flush_search_url_rewrite() {
    if ( get_option( 'xo_search_url_rewrite_flushed' ) ) {
        return;
    }

    flush_rewrite_rules();
    update_option( 'xo_search_url_rewrite_flushed', '1' );
}
add_action( 'init', 'xo_maybe_flush_search_url_rewrite', 20 );
