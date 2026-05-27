<?php
/**
 * Plugin Name: Exo-functions
 * Plugin URI: http://wadellc.co
 * Description: Utilities optimized for FSE Block Themes. Extends Gravity Forms, and WooCommerce.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 1.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin directory constant
define( 'XO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/**
 * 1. ENVIRONMENT CUE (Visual Border)
 */
add_action( 'admin_head', function() {
    $env = wp_get_environment_type();
    $colors = [
        'local'       => '#00bfff', 
        'development' => '#41ab4f', 
        'staging'     => '#e8a541', 
        'production'  => '#ef4917', 
    ];
    $border_color = isset( $colors[ $env ] ) ? $colors[ $env ] : '';

    if ( $border_color ) {
        echo "<style>
            body.wp-admin::after {
                content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                border-top: 3px solid $border_color; pointer-events: none; z-index: 999999; box-sizing: border-box;
            }
        </style>";
    }
});


/**
 * 2. GLOBAL SITE INITIALIZATION HOOK
 */
add_action( 'plugins_loaded', function() {
    do_action( 'exo_functions_loaded' );
}, 20 );


/**
 * 3. EXTENSION LOADER MAP & LICENSE INJECTION
 * Multi-site and single-site compatible core processor.
 */
function exo_load_plugin_extensions() {
    global $exo_active_exts;
    $exo_active_exts = array();
    $extend_path = XO_PLUGIN_DIR . 'extend/';

    // 3a. Pull in the Settings Interface file unconditionally
    if ( file_exists( XO_PLUGIN_DIR . 'xo-wp-settings.php' ) ) {
        require_once XO_PLUGIN_DIR . 'xo-wp-settings.php';
    }

    // 3b. Robust Hybrid Settings Grabber
    // Checks the Network option first, falls back to local site option seamlessly
    if ( is_multisite() ) {
        $saved_settings = get_network_option( get_main_site_id(), 'exo_plugin_settings', [] );
        if ( empty( $saved_settings ) ) {
            $saved_settings = get_option( 'exo_plugin_settings', [] );
        }
    } else {
        $saved_settings = get_option( 'exo_plugin_settings', [] );
    }

    // 3c. Direct License Constant Auto-Injection
    // If the key is present in your settings and the constant isn't defined yet, fire it up.
    $license_map = [
        'gf_license_key'  => 'GF_LICENSE_KEY',
        'gpp_license_key' => 'GPP_LICENSE_KEY',
        'akismet_api_key' => 'WPCOM_API_KEY',
    ];

    foreach ( $license_map as $settings_key => $constant_name ) {
        if ( ! empty( $saved_settings[$settings_key] ) && ! defined( $constant_name ) ) {
            define( $constant_name, $saved_settings[$settings_key] );
        }
    }

    // 3d. Set Fallback Toggles if settings don't exist yet (Fresh Install)
    $saved_toggles = $saved_settings;
    if ( ! isset( $saved_toggles['is_initialized'] ) ) {
        $saved_toggles = [ 'wp-core' => 1, 'wp-frontend' => 1, 'gravity-forms' => 1, 'woo-commerce' => 1 ];
    }

    // 3e. Evaluate active status based on database settings and plugin availability
    $map = array(
        'wp-core/index.php'        => array( 'active' => isset( $saved_toggles['wp-core'] ),                                          'label' => 'Admin Tools' ),
        'wp-frontend/index.php'    => array( 'active' => isset( $saved_toggles['wp-frontend'] ),                                      'label' => 'Frontend Tools' ),
        'gravity-forms/index.php'  => array( 'active' => ( class_exists( 'GFCommon' ) && isset( $saved_toggles['gravity-forms'] ) ),  'label' => 'Gravity Forms' ),
        'woo-commerce/index.php'   => array( 'active' => ( class_exists( 'WooCommerce' ) && isset( $saved_toggles['woo-commerce'] ) ), 'label' => 'WooCommerce' ),
    );

    // 3f. Execute loaders
    foreach ( $map as $file => $data ) {
        if ( $data['active'] && file_exists( $extend_path . $file ) ) {
            require_once $extend_path . $file;
            $exo_active_exts[] = $data['label'];
        }
    }
}
add_action( 'plugins_loaded', 'exo_load_plugin_extensions', 10 );


/**
 * 4. PLUGIN ROW DASHBOARD DISPLAY
 */
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file ) {
    global $exo_active_exts;
    if ( $plugin_file === plugin_basename( __FILE__ ) ) {
        if ( ! empty( $exo_active_exts ) ) {
            $plugin_meta[] = '<span style="color:#2F4D2F;"><strong>Active Extensions:</strong> ' . esc_html( implode( ', ', $exo_active_exts ) ) . '</span>';
        } else {
            $plugin_meta[] = '<span style="color:#999;">No extensions loaded</span>';
        }
    }
    return $plugin_meta;
}, 10, 2 );


/**
 * 5. PLUGIN Update Checker Integration (GitHub Branch-Based Updates)
 * Connects directly to your public repository for seamless, fleet-wide updates.
 */
if ( file_exists( XO_PLUGIN_DIR . 'includes/plugin-update-checker-5.6/plugin-update-checker.php' ) ) {
    require_once XO_PLUGIN_DIR . 'includes/plugin-update-checker-5.6/plugin-update-checker.php';

    \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/wadellc/xo-functions',
        __FILE__,
        'xo-functions'
    );
}