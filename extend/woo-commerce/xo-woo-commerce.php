<?php
/**
 * Extension Name: WooCommerce Extensions
 * Description: Optimizes product administration workflows by exposing shipping classes and clarifying taxonomy labels.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. PRODUCT LIST COLUMNS: SHIPPING CLASS
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
        echo '<span style="color:#999;"><em>' . esc_html__( 'n/a', 'woocommerce' ) . '</em></span>';
    }
}, 10, 2 );

/**
 * 2. TAXONOMY LABEL CLARIFICATION
 * Modifies the core 'product_cat' schema object variables to differentiate them from post categories.
 */
add_action( 'registered_taxonomy', function( $taxonomy, $object_type, $args ) {
    if ( 'product_cat' === $taxonomy ) {
        global $wp_taxonomies;
        
        if ( isset( $wp_taxonomies[$taxonomy]->labels ) ) {
            // Update structural global labels safely for custom taxonomy panels
            $wp_taxonomies[$taxonomy]->labels->singular_name = esc_html__( 'Product Category', 'woocommerce' );
            $wp_taxonomies[$taxonomy]->labels->name          = esc_html__( 'Product Categories', 'woocommerce' );
        }
    }
}, 10, 3 );