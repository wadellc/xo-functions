<?php
/**
 * Extension Name: Frontend Tools
 * Description: Client-facing utilities, shortcodes, and content layout blocks.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the module directory
$wp_frontend_dir = dirname( __FILE__ );

// Load all frontend extensions
require_once $wp_frontend_dir . '/wp-frontend.php';
