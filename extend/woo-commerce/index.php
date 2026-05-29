<?php
/**
 * WooCommerce Extensions Loader.
 *
 * Main entry point for the WooCommerce custom optimization layout tools.
 * Evaluates execution dependencies and safely pulls in custom sub-scripts.
 *
 * @package    XO_Functions
 * @subpackage WooCommerce
 * @category   Loaders
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.0.0
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the module directory
$xo_woo_dir = dirname( __FILE__ );

// Load all WooCommerce engine overrides safely
if ( file_exists( $xo_woo_dir . '/xo-woocommerce.php' ) ) {
    require_once $xo_woo_dir . '/xo-woocommerce.php';
}