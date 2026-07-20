<?php
/**
 * The Template for displaying products in a product category. Simply includes the archive template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/taxonomy-product_cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 *
 * Overriden for UTRF
 * Custom category Templates for TennXC, SNAPP, PEM and more.
 * UTRF clients are not likely to purchase products outside of each category.
 * SNAPP may have sub-categories : to be developed.
 * 
 */

echo '<script>console.log("woo-tax-prod-cat")</script>';

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Default product template: 
// wc_get_template( 'archive-product.php' );
wc_get_template( 'archive-utrf-product.php' );
