<?php
/**
 * Plugin Name: Exo-functions
 * Plugin URI: http://wadellc.co
 * Description: Hybrid-compatible custom utilities optimized for Block Themes, Gravity Forms, and WooCommerce. Works seamlessly on single-site and multisite setups.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
 */
function exo_load_plugin_extensions() {
    global $exo_active_exts;
    $exo_active_exts = array();
    $plugin_root = plugin_dir_path( __FILE__ );
    $extend_path = $plugin_root . 'extend/';

    // 3a. Pull in the Settings Interface file unconditionally
    if ( file_exists( $plugin_root . 'xo-wp-settings.php' ) ) {
        require_once $plugin_root . 'xo-wp-settings.php';
    }

    // 3b. Environment-Aware Settings Grabber
    if ( is_multisite() ) {
        $saved_settings = get_network_option( get_main_site_id(), 'exo_plugin_settings', [] );
    } else {
        $saved_settings = get_option( 'exo_plugin_settings', [] );
    }

    // 3c. License Constant Auto-Injection (With Domain Whitelist for Multisite)
    $should_inject = true;

    if ( is_multisite() ) {
        $current_site = get_site();
        $current_domain = isset( $current_site->domain ) ? $current_site->domain : '';
        
        // ADD AUTHORIZED MULTISITE DOMAINS HERE
        $authorized_domains = [
            'yourmainagency.com',
            'wagners-retail.com',
        ];
        
        if ( ! in_array( $current_domain, $authorized_domains, true ) ) {
            $should_inject = false;
        }
    }

    if ( $should_inject ) {
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
    }

    // 3d. Set Fallback Toggles if settings don't exist yet
    $saved_toggles = $saved_settings;
    if ( empty( $saved_toggles ) ) {
        $saved_toggles = [ 'xo-wp-core' => 1, 'xo-wp-frontend' => 1, 'xo-gravity-forms' => 1, 'xo-woo-commerce' => 1 ];
    }
    
    // 3e. Evaluate active status based on database settings and plugin availability
    $map = array(
        'xo-wp-core.php'        => array( 'active' => isset( $saved_toggles['xo-wp-core'] ), 'label'  => 'Admin Tools' ),
        'xo-wp-frontend.php'    => array( 'active' => isset( $saved_toggles['xo-wp-frontend'] ), 'label'  => 'Frontend Tools' ),
        'xo-gravity-forms.php'  => array( 'active' => ( class_exists( 'GFCommon' ) && isset( $saved_toggles['xo-gravity-forms'] ) ), 'label'  => 'Gravity Forms' ),
        'xo-woo-commerce.php'   => array( 'active' => ( class_exists( 'WooCommerce' ) && isset( $saved_toggles['xo-woo-commerce'] ) ), 'label'  => 'WooCommerce' ),
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
