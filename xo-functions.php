<?php
/**
 * Plugin Name: XO Functions
 * Description: WordPress and plugin support functions and utilities. Enviroment cues, helper functions, and more.
 * Author:      David W. Couch <http://wadellc.co>
 * Version:     2.1.2
 * Text Domain: xo-functions
 * Requires at least: 5.6
 * Requires PHP:      7.4
 *
 * @package    XO_Functions
 * @subpackage Core
 * @category   Framework
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.2
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. GLOBAL SYSTEM CONSTANTS
if ( ! function_exists( 'get_plugin_data' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$xo_plugin_data = get_plugin_data( __FILE__ );

define( 'XO_VERSION', $xo_plugin_data['Version'] ); // Dynamically grabs plugin Version from header comment
define( 'XO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// 2. LOAD ADMINISTRATIVE INTERFACE
if ( is_admin() && file_exists( XO_PLUGIN_DIR . 'admin/xo-wp-settings.php' ) ) {
    require_once XO_PLUGIN_DIR . 'admin/xo-wp-settings.php';
}

// 3. RUN THE UTILITY MODULE LOADER PIPELINE
add_action( 'plugins_loaded', 'xo_functions_core_module_loader' );
function xo_functions_core_module_loader() {
    $extend_path = XO_PLUGIN_DIR . 'extend/';
    
    // 3a. Pull saved configuration states safely based on site topology
    $saved_settings = is_multisite() ? get_site_option( 'xo_functions_settings', array() ) : get_option( 'xo_functions_settings', array() );
    
    // 3b. Establish fallbacks for uninitialized site instances
    $saved_toggles = $saved_settings;
    if ( ! isset( $saved_toggles['is_initialized'] ) ) {
        $saved_toggles = array(
            'wp-core'       => 1,
            'wp-frontend'   => 1,
            'gravity-forms' => 1,
            'woo-commerce'  => 1,
        );
    }

    // 3c. Evaluate active validation map boundaries safely
    $module_map = array(
        'wp-core/index.php'       => array( 'active' => isset( $saved_toggles['wp-core'] ) ),
        'wp-frontend/index.php'   => array( 'active' => isset( $saved_toggles['wp-frontend'] ) ),
        'gravity-forms/index.php' => array( 'active' => ( class_exists( 'GFCommon' ) && isset( $saved_toggles['gravity-forms'] ) ) ),
        'woo-commerce/index.php'  => array( 'active' => ( class_exists( 'WooCommerce' ) && isset( $saved_toggles['woo-commerce'] ) ) ),
    );

    // 3d. Execute paths safely using file system check blocks
    foreach ( $module_map as $file => $data ) {
        if ( $data['active'] && file_exists( $extend_path . $file ) ) {
            require_once $extend_path . $file;
        }
    }
}