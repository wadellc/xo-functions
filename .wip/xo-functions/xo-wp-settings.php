<?php
/**
 * Extension Name: Plugin Settings UI
 * Description: Hybrid Control Panel. Handles Network Admin routing for multisite and hides menus from child sites.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. HYBRID INTERFACE REGISTER
 * If Multisite: Places page in Network Admin dashboard, hiding it completely from child sites.
 * If Single site: Places page under standard Settings > Exo-functions.
 */
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', function() {
    if ( is_multisite() ) {
        add_submenu_page(
            'settings.php',
            'Exo-functions Network Manager',
            'Exo-functions',
            'manage_network',
            'exo-functions-settings',
            'exo_render_settings_page'
        );
    } else {
        add_options_page(
            'Exo-functions Settings',
            'Exo-functions',
            'manage_options',
            'exo-functions-settings',
            'exo_render_settings_page'
        );
    }
});

/**
 * 2. RENDER THE HYBRID SETTINGS UI
 */
function exo_render_settings_page() {
    // Capability Enforcement
    $required_cap = is_multisite() ? 'manage_network' : 'manage_options';
    if ( ! current_user_can( $required_cap ) ) {
        return;
    }

    // Save Logic (Hybrid Storage)
    if ( isset( $_POST['exo_settings_nonce'] ) && wp_verify_nonce( $_POST['exo_settings_nonce'], 'exo_save_settings' ) ) {
        $saved_settings = [];

        // Modules Processing
        $allowed_tools = [ 'xo-wp-core', 'xo-wp-frontend', 'xo-gravity-forms', 'xo-woo-commerce' ];
        $submitted_tools = isset( $_POST['enabled_tools'] ) && is_array( $_POST['enabled_tools'] ) ? $_POST['enabled_tools'] : [];
        foreach ( $submitted_tools as $tool ) {
            if ( in_array( $tool, $allowed_tools, true ) ) {
                $saved_settings[$tool] = 1;
            }
        }

        // License Keys Processing
        $license_keys = [ 'gf_license_key', 'gpp_license_key', 'akismet_api_key' ];
        foreach ( $license_keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $saved_settings[$key] = sanitize_text_field( $_POST[$key] );
            }
        }
        
        if ( is_multisite() ) {
            update_network_option( get_main_site_id(), 'exo_plugin_settings', $saved_settings );
        } else {
            update_option( 'exo_plugin_settings', $saved_settings );
        }
        echo '<div class="updated"><p>Settings saved successfully.</p></div>';
    }

    // Fetch Database Context
    $settings = is_multisite() ? get_network_option( get_main_site_id(), 'exo_plugin_settings', [] ) : get_option( 'exo_plugin_settings', [] );

    $tools_manifest = [
        'xo-wp-core'        => [ 'title' => 'Admin Tools', 'desc' => 'DevOps border, Admin bar templates, Page columns, and Install dates.' ],
        'xo-wp-frontend'    => [ 'title' => 'Frontend Tools', 'desc' => 'Content layout extensions and the [todays_date] shortcode.' ],
        'xo-gravity-forms'  => [ 'title' => 'Gravity Forms Extensions', 'desc' => 'Handles layout overrides for forms engine modules.' ],
        'xo-woo-commerce'   => [ 'title' => 'WooCommerce Extensions', 'desc' => 'Conditional layout fixes for eCommerce checkouts.' ],
    ];

    $gf_key      = isset( $settings['gf_license_key'] ) ? $settings['gf_license_key'] : '';
    $gpp_key     = isset( $settings['gpp_license_key'] ) ? $settings['gpp_license_key'] : '';
    $akismet_key = isset( $settings['akismet_api_key'] ) ? $settings['akismet_api_key'] : '';
    ?>
    <div class="wrap">
        <h1>Exo-functions Dashboard Panel (<?php echo is_multisite() ? 'Multisite Network Mode' : 'Single Site Mode'; ?>)</h1>
        <p>Centralized configuration engine for your premium extension scripts.</p>
        <hr />
        
        <form method="post" action="">
            <?php wp_nonce_field( 'exo_save_settings', 'exo_settings_nonce' ); ?>
            
            <h2>1. Structural Framework Modules</h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <?php foreach ( $tools_manifest as $slug => $info ) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html( $info['title'] ); ?></th>
                            <td>
                                <label for="<?php echo esc_attr( $slug ); ?>">
                                    <input name="enabled_tools[]" type="checkbox" id="<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php checked( empty($settings) || isset( $settings[$slug] ) ); ?>>
                                    <?php echo esc_html( $info['desc'] ); ?>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>2. Centralized Licensing Repository</h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="gf_license_key">Gravity Forms Key</label></th>
                        <td><input name="gf_license_key" type="password" id="gf_license_key" value="<?php echo esc_attr( $gf_key ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gpp_license_key">Gravity Perks Pro Key</label></th>
                        <td><input name="gpp_license_key" type="password" id="gpp_license_key" value="<?php echo esc_attr( $gpp_key ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="akismet_api_key">Akismet API Key</label></th>
                        <td><input name="akismet_api_key" type="password" id="akismet_api_key" value="<?php echo esc_attr( $akismet_key ); ?>" class="regular-text"></td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button( 'Save All Parameters' ); ?>
        </form>
    </div>
    <?php
}
