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
 * @version    2.0.0
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

    $product = wc_get_product( absint( $product_id ) );

    if ( is_a( $product, 'WC_Product' ) ) {
        $shipping_class_id = $product->get_shipping_class_id();
        
        if ( $shipping_class_id > 0 ) {
            $shipping_class = get_term( $shipping_class_id, 'product_shipping_class' );
            
            // Output the class name safely if it exists and returns no errors
            if ( $shipping_class && ! is_wp_error( $shipping_class ) ) {
                echo esc_html( $shipping_class->name );
                return;
            }
        }
        
        // Fallback layout state if no shipping class is configured
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
        // Update structural labels safely for custom taxonomy panels
        $taxonomy_object->labels->singular_name = esc_html__( 'Product Category', 'woocommerce' );
        $taxonomy_object->labels->name          = esc_html__( 'Product Categories', 'woocommerce' );
    }
}, 99 );