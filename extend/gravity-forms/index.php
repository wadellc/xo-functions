<?php
/**
 * Gravity Forms Extensions Loader.
 *
 * Main entry point for the Gravity Forms optimization suite. Evaluates 
 * structural dependencies and safely pulls in custom engine overrides.
 *
 * @package    XO_Functions
 * @subpackage Gravity_Forms
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
$xo_gf_dir = dirname( __FILE__ );

// Load all Gravity Forms extensions safely
if ( file_exists( $xo_gf_dir . '/xo-gravity-forms.php' ) ) {
    require_once $xo_gf_dir . '/xo-gravity-forms.php';
}