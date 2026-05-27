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

// Define the module directory
$woo_commerce_dir = dirname( __FILE__ );

// Load all WooCommerce extensions
require_once $woo_commerce_dir . '/xo-woo-commerce.php';
