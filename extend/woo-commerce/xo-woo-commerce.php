<?php
/**
 * WooCommerce Custom Admin Optimization Utilities.
 *
 * Appends custom tracking data grids into core product listings and clarifies
 * eCommerce taxonomy label variables to provide clear administrative direction.
 *
 * @package    XO_Functions
 * @subpackage WooCommerce
 * @category   Dashboard_Filters
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.7
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. PRODUCT LIST COLUMNS: SHIPPING CLASS
 *
 * Appends a custom shipping class tracking metric column to the dashboard layout interface.
 */
add_filter( 'manage_edit-product_columns', function( $columns ) {
    $columns['shipping_class'] = esc_html__( 'Shipping Class', 'woocommerce' );
    return $columns;
}, 999 );

add_action( 'manage_product_posts_custom_column', function( $column, $product_id ) {
    if ( 'shipping_class' !== $column ) {
        return;
    }

    try {
        $product = wc_get_product( absint( $product_id ) );

        if ( ! is_a( $product, 'WC_Product' ) ) {
            echo '<span style="color:#999;"><em>' . esc_html__( 'n/a', 'xo-functions' ) . '</em></span>';
            return;
        }

        $shipping_class_id = $product->get_shipping_class_id();

        if ( $shipping_class_id > 0 ) {
            $shipping_class = get_term( absint( $shipping_class_id ), 'product_shipping_class' );

            if ( $shipping_class && ! is_wp_error( $shipping_class ) ) {
                echo esc_html( $shipping_class->name );
                return;
            }

            if ( is_wp_error( $shipping_class ) ) {
                error_log( '[XO-FUNCTIONS ERROR] manage_product_posts_custom_column: get_term failed for shipping class ID ' . $shipping_class_id . ' on product ' . absint( $product_id ) . ' — ' . $shipping_class->get_error_message() );
            }
        }

        // Fallback: no shipping class configured or lookup failed.
        echo '<span style="color:#999;"><em>' . esc_html__( 'n/a', 'xo-functions' ) . '</em></span>';

    } catch ( \Throwable $e ) {
        error_log( '[XO-FUNCTIONS ERROR] manage_product_posts_custom_column: exception on product ' . absint( $product_id ) . ' — ' . $e->getMessage() );
        echo '<span style="color:#999;"><em>' . esc_html__( 'n/a', 'xo-functions' ) . '</em></span>';
    }
}, 10, 2 );

/**
 * 2. TAXONOMY LABEL CLARIFICATION
 *
 * Safely targets and adjusts the 'product_cat' object labels on init
 * to cleanly isolate store catalogs from post categories.
 */
add_action( 'init', function() {
    $taxonomy_object = get_taxonomy( 'product_cat' );

    if ( $taxonomy_object && isset( $taxonomy_object->labels ) ) {
        $taxonomy_object->labels->singular_name = esc_html__( 'Product Category', 'woocommerce' );
        $taxonomy_object->labels->name          = esc_html__( 'Product Categories', 'woocommerce' );
    }
}, 99 );
