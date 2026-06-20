<?php
/**
 * Plugin Name: XO Functions
 * Description: WordPress and plugin support functions and utilities. Environment cues, helper functions, and more.
 * Author:      David W. Couch <http://wadellc.co>
 * Version:     2.1.7
 * Text Domain: xo-functions
 * Requires at least: 5.6
 * Requires PHP:      7.4
 *
 * @package    XO_Functions
 * @subpackage Core
 * @category   Framework
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.7
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. GLOBAL SYSTEM CONSTANTS
define( 'XO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// 2. ENVIRONMENT CUE (Visual Border)
add_action( 'admin_head', function() {
    $env = wp_get_environment_type();
    $colors = [
        'local'       => '#00bfff',
        'development' => '#41ab4f',
        'staging'     => '#e8a541',
        'production'  => '#ef4917',
    ];

    if ( ! isset( $colors[ $env ] ) ) {
        return;
    }

    $border_color = esc_attr( $colors[ $env ] );
    echo "<style>
        body.wp-admin::after {
            content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            border-top: 3px solid {$border_color}; pointer-events: none; z-index: 999999; box-sizing: border-box;
        }
    </style>";
} );

// 3. LOAD ADMINISTRATIVE INTERFACE
if ( is_admin() && file_exists( XO_PLUGIN_DIR . 'xo-wp-settings.php' ) ) {
    require_once XO_PLUGIN_DIR . 'xo-wp-settings.php';
}

// 4. RUN THE UTILITY MODULE LOADER PIPELINE
add_action( 'plugins_loaded', 'xo_functions_core_module_loader' );
function xo_functions_core_module_loader() {
    $extend_path = XO_PLUGIN_DIR . 'extend/';

    // 4a. Pull saved configuration states safely based on site topology.
    $saved_settings = is_multisite()
        ? get_site_option( 'xo_functions_settings', array() )
        : get_option( 'xo_functions_settings', array() );

    // 4b. Establish defaults for uninitialized instances.
    $saved_toggles = xo_get_default_toggles( $saved_settings );

    // 4c. Evaluate active validation map boundaries safely.
    $module_map = array(
        'wp-core/index.php'       => array( 'active' => isset( $saved_toggles['wp-core'] ) ),
        'wp-frontend/index.php'   => array( 'active' => isset( $saved_toggles['wp-frontend'] ) ),
        'gravity-forms/index.php' => array( 'active' => ( class_exists( 'GFCommon' ) && isset( $saved_toggles['gravity-forms'] ) ) ),
        'woo-commerce/index.php'  => array( 'active' => ( class_exists( 'WooCommerce' ) && isset( $saved_toggles['woo-commerce'] ) ) ),
    );

    // 4d. Execute paths safely using file system check blocks.
    foreach ( $module_map as $file => $data ) {
        if ( $data['active'] && file_exists( $extend_path . $file ) ) {
            require_once $extend_path . $file;
        }
    }
}

/**
 * Returns the saved settings array, substituting plugin defaults when the
 * site has never been configured (is_initialized key absent).
 *
 * Centralises the default-toggle logic that was previously duplicated across
 * three separate call sites.
 *
 * @param  array $saved_settings Raw value from get_option / get_site_option.
 * @return array
 */
function xo_get_default_toggles( $saved_settings ) {
    if ( isset( $saved_settings['is_initialized'] ) ) {
        return $saved_settings;
    }

    return array(
        'wp-core'       => 1,
        'wp-frontend'   => 1,
        'gravity-forms' => 1,
        'woo-commerce'  => 1,
    );
}


// 5. PLUGIN ROW DASHBOARD DISPLAY
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file ) {
    if ( $plugin_file !== plugin_basename( __FILE__ ) ) {
        return $plugin_meta;
    }

    $saved_settings = is_multisite()
        ? get_site_option( 'xo_functions_settings', array() )
        : get_option( 'xo_functions_settings', array() );

    $saved_settings = xo_get_default_toggles( $saved_settings );

    $label_map = array(
        'wp-core'       => 'Admin Tools',
        'wp-frontend'   => 'Frontend Tools',
        'gravity-forms' => 'Gravity Forms',
        'woo-commerce'  => 'WooCommerce',
    );

    $active = array();
    foreach ( $label_map as $key => $label ) {
        if ( isset( $saved_settings[ $key ] ) ) {
            $active[] = $label;
        }
    }

    if ( ! empty( $active ) ) {
        $plugin_meta[] = '<span style="color:#2F4D2F;"><strong>Active Extensions:</strong> ' . esc_html( implode( ', ', $active ) ) . '</span>';
    } else {
        $plugin_meta[] = '<span style="color:#999;">No extensions loaded</span>';
    }

    return $plugin_meta;
}, 10, 2 );


// 6. PLUGIN UPDATE CHECKER
// FIX: PHP fatal — namespace `use` declarations cannot appear inside a
//      conditional block. Using the fully-qualified class name instead.
$xo_puc_bootstrap = XO_PLUGIN_DIR . 'includes/plugin-update-checker-5.6/plugin-update-checker.php';

if ( file_exists( $xo_puc_bootstrap ) ) {
    try {
        require_once $xo_puc_bootstrap;

        \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/wadellc/xo-functions',
            __FILE__,
            'xo-functions'
        );
    } catch ( \Throwable $e ) {
        error_log( '[XO-FUNCTIONS ERROR] Plugin Update Checker failed to initialise: ' . $e->getMessage() );
    }
}
