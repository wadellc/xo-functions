<?php
/**
 * Extension Name: Gravity Forms Extensions
 * Description: Optimizes form list views, restricts defaults to active entries, and exposes submission tracking columns.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the module directory
$gravity_forms_dir = dirname( __FILE__ );

// Load all Gravity Forms extensions
require_once $gravity_forms_dir . '/xo-gravity-forms.php';
