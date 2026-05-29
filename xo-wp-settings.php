<?php
/**
 * XO Functions Administrative Control Panel.
 *
 * Generates an adaptive administration interface that scales perfectly 
 * between standalone single sites and global network admin environments.
 *
 * @package    XO_Functions
 * @subpackage Admin
 * @category   Settings
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.1
 * @since      1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. DYNAMIC MENU REGISTRATION
 * Hooks into the Network Admin for Multisite networks, or the standard Tools
 * menu for standalone single-site deployments.
 */
if ( is_multisite() ) {
    add_action( 'network_admin_menu', 'xo_register_settings_menu' );
} else {
    add_action( 'admin_menu', 'xo_register_settings_menu' );
}

function xo_register_settings_menu() {
    // Determine appropriate capability and hook wrapper based on context
    $capability = is_multisite() ? 'manage_network_options' : 'manage_options';
    
    add_management_page(
        esc_html__( 'XO Functions Engine', 'xo-functions' ),
        esc_html__( 'XO Functions', 'xo-functions' ),
        $capability,
        'xo-functions-settings',
        'xo_render_settings_page_html'
    );
}

/**
 * 2. HYBRID DATABASE SAVING ENGINE
 * Single sites use the native options.php handler. Multisite networks capture
 * the post request directly to prevent data isolation across sub-sites.
 */
add_action( 'admin_init', function() {
    // Scenario A: Standard Single Site Registration
    if ( ! is_multisite() ) {
        register_setting( 'xo_functions_group', 'xo_functions_settings', 'xo_sanitize_settings_input' );
        return;
    }

    // Scenario B: Multisite Network Form Submission Interceptor
    if ( isset( $_POST['xo_multisite_nonce'] ) && wp_verify_nonce( $_POST['xo_multisite_nonce'], 'xo_save_network_settings' ) ) {
        if ( ! current_user_can( 'manage_network_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'xo-functions' ) );
        }

        $raw_input = isset( $_POST['xo_functions_settings'] ) ? $_POST['xo_functions_settings'] : array();
        $sanitized_data = xo_sanitize_settings_input( $raw_input );
        
        update_site_option( 'xo_functions_settings', $sanitized_data );
        
        // Redirect back to the network settings screen with a success flag
        wp_safe_redirect( add_query_arg( array( 'page' => 'xo-functions-settings', 'updated' => 'true' ), network_admin_url( 'settings.php' ) ) );
        exit;
    }
});

/**
 * 2b. Universal Sanitization Callback
 */
function xo_sanitize_settings_input( $input ) {
    $sanitized = array( 'is_initialized' => 1 );
    $valid_keys = array( 'wp-core', 'wp-frontend', 'gravity-forms', 'woo-commerce' );
    
    foreach ( $valid_keys as $key ) {
        if ( isset( $input[ $key ] ) && '1' === $input[ $key ] ) {
            $sanitized[ $key ] = 1;
        }
    }
    return $sanitized;
}

/**
 * 3. ADAPTIVE HTML VIEW WORKSPACE
 */
function xo_render_settings_page_html() {
    $needed_cap = is_multisite() ? 'manage_network_options' : 'manage_options';
    if ( ! current_user_can( $needed_cap ) ) {
        return;
    }

    // Fetch the correct configuration data array based on the system state
    $saved_settings = is_multisite() ? get_site_option( 'xo_functions_settings', array() ) : get_option( 'xo_functions_settings', array() );
    
    // Automatically pre-fill toggles if the system hasn't been configured yet
    if ( ! isset( $saved_settings['is_initialized'] ) ) {
        $saved_settings = array(
            'wp-core'       => 1,
            'wp-frontend'   => 1,
            'gravity-forms' => 1,
            'woo-commerce'  => 1,
        );
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p class="description">
            <?php 
            is_multisite() 
                ? esc_html_e( 'Configure global modules across your entire Network Fleet cleanly.', 'xo-functions' )
                : esc_html_e( 'Toggle global modules across your production environment safely.', 'xo-functions' ); 
            ?>
        </p>

        <?php if ( is_multisite() && isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e( 'Network settings saved successfully.', 'xo-functions' ); ?></strong></p></div>
        <?php endif; ?>
        <hr />

        <form method="post" action="<?php echo is_multisite() ? '' : 'options.php'; ?>">
            <?php
            if ( is_multisite() ) {
                wp_nonce_field( 'xo_save_network_settings', 'xo_multisite_nonce' );
            } else {
                settings_fields( 'xo_functions_group' );
            }

            $modules = array(
                'wp-core'       => array( 'label' => esc_html__( 'Admin Core Tools', 'xo-functions' ), 'desc' => esc_html__( 'Enables administrative extensions and tree tools.', 'xo-functions' ) ),
                'wp-frontend'   => array( 'label' => esc_html__( 'Frontend Utilities', 'xo-functions' ), 'desc' => esc_html__( 'Enables classes, selectors, and user shortcodes.', 'xo-functions' ) ),
                'gravity-forms' => array( 'label' => esc_html__( 'Gravity Forms Engine', 'xo-functions' ), 'desc' => esc_html__( 'Optimizes active views and tracks submissions.', 'xo-functions' ), 'dep' => 'GFCommon' ),
                'woo-commerce'  => array( 'label' => esc_html__( 'WooCommerce Utilities', 'xo-functions' ), 'desc' => esc_html__( 'Optimizes product attributes and category layouts.', 'xo-functions' ), 'dep' => 'WooCommerce' ),
            );
            ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <?php foreach ( $modules as $key => $data ) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html( $data['label'] ); ?></th>
                            <td>
                                <fieldset>
                                    <label for="<?php echo esc_attr( $key ); ?>">
                                        <input 
                                            type="checkbox" 
                                            name="xo_functions_settings[<?php echo esc_attr( $key ); ?>]" 
                                            id="<?php echo esc_attr( $key ); ?>" 
                                            value="1" 
                                            <?php checked( 1, isset( $saved_settings[ $key ] ) ); ?>
                                        />
                                        <?php echo esc_html( $data['desc'] ); ?>
                                    </label>
                                    <?php if ( isset( $data['dep'] ) && ! class_exists( $data['dep'] ) ) : ?>
                                        <p class="description" style="color: #d63638; font-weight: 500;">
                                            <?php printf( esc_html__( '⚠️ Inactive: Requires %s plugin dependency to execute.', 'xo-functions' ), esc_html( $data['dep'] ) ); ?>
                                        </p>
                                    <?php endif; ?>
                                </fieldset>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}