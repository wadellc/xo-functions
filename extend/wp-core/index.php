<?php
/**
 * WordPress Core Utilities Extension Loader.
 *
 * Main entry point for administrative utilities. Evaluates active state
 * and safely pulls in specific sub-modules.
 *
 * @package    XO_Functions
 * @subpackage Core_Utilities
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
$xo_core_dir = dirname( __FILE__ );

// Load all core utility modules safely
if ( file_exists( $xo_core_dir . '/subcategories-add.php' ) ) {
    require_once $xo_core_dir . '/subcategories-add.php';
}