<?php
/**
 * Extend Woo Commerce
 */


/**
 * Add Shipping Class column to WooCommerce Product List
 * Includes check for WooCommerce install
 */
if ( class_exists( 'WooCommerce' ) ) {

    add_filter( 'manage_edit-product_columns', 'add_admin_products_shipping_class_column', 999 );
    function add_admin_products_shipping_class_column( $columns ) {
        $columns['shipping_class'] = __( 'Shipping Class', 'woocommerce' );
        return $columns;
    }

    add_action( 'manage_product_posts_custom_column', 'get_admin_products_shipping_class_column_content', 10, 2 );
    function get_admin_products_shipping_class_column_content( $column, $product_id ) {
        if ( $column === 'shipping_class' ) {
            $product = wc_get_product( $product_id );

            if ( is_a( $product, 'WC_Product' ) ) {
                $shipping_class_id = $product->get_shipping_class_id();
                $shipping_class    = get_term( $shipping_class_id, 'product_shipping_class' );
                
                // Check if class exists and isn't an error or empty
                if ( $shipping_class && ! is_wp_error( $shipping_class ) ) {
                    echo esc_html( $shipping_class->name );
                } else {
                    echo '<span style="color:#999;"><em>n/a</em></span>';
                }
            }
        }
    }

    // Force Woo Product Categoies to display type 'Product Category' vs the same as post 'Category'
    add_action( 'registered_taxonomy', function( $taxonomy, $object_type, $args ) {
        if ( 'product_cat' === $taxonomy ) {
            global $wp_taxonomies;
            // Update the singular and general labels used in the admin UI
            $wp_taxonomies[$taxonomy]->labels->singular_name = 'Product Category';
            $wp_taxonomies[$taxonomy]->labels->name = 'Product Categories';
        }
    }, 10, 3 );






}

