<?php
/**
 * Frontend Tools Extension Loader.
 *
 * Main entry point for client-facing utility components. Evaluates 
 * structural file states and orchestrates decoupled frontend actions and shortcodes.
 *
 * @package    XO_Functions
 * @subpackage Frontend_Tools
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
$xo_frontend_dir = dirname( __FILE__ );

// 1. Load general structural frontend extensions/hooks safely
if ( file_exists( $xo_frontend_dir . '/wp-frontend.php' ) ) {
    require_once $xo_frontend_dir . '/wp-frontend.php';
}

// 2. Load the isolated shortcodes engine safely
if ( file_exists( $xo_frontend_dir . '/shortcodes.php' ) ) {
    require_once $xo_frontend_dir . '/shortcodes.php';
}