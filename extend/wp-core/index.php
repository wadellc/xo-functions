<?php
/**
 * Extension Name: WordPress Core Utilities
 * Description: Bundled core enhancements for administrative layout, page tracking, and debugging.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the module directory
$wp_core_dir = dirname( __FILE__ );

// Load all core utility modules
require_once $wp_core_dir . '/slug-to-body-class.php';
require_once $wp_core_dir . '/dynamic-taxonomies-menus.php';
require_once $wp_core_dir . '/subcategories-add.php';
