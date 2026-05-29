<?php
/**
 * XO Functions Administrative Control Panel.
 *
 * Generates the clean dashboard menu interface, processes sanitized form posts,
 * and maintains unified toggle states across fleet environments.
 *
 * @package    XO_Functions
 * @subpackage Admin
 * @category   Settings
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.0.0
 * @since      1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Register Admin Dashboard Menu Link
 */
add_action( 'admin_menu', function() {
    add_management_page(
        esc_html__( 'XO Functions Engine', 'xo-functions' ),
        esc_html__( 'XO Functions', 'xo-functions' ),
        'manage_options',
        'xo-functions-settings',
        'xo_render_settings_page_html'
    );
});

/**
 * 2. Sanitize and Process Option Forms
 */
add_action( 'admin_init', function() {
    register_setting( 'xo_functions_group', 'xo_functions_settings', function( $input ) {
        $sanitized = array();
        $sanitized['is_initialized'] = 1;

        $valid_keys = array( 'wp-core', 'wp-frontend', 'gravity-forms', 'woo-commerce' );
        foreach ( $valid_keys as $key ) {
            if ( isset( $input[ $key ] ) && '1' === $input[ $key ] ) {
                $sanitized[ $key ] = 1;
            }
        }
        return $sanitized;
    });
});

/**
 * 3. Render HTML Administration View Workspace
 */
function xo_render_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $saved_settings = get_option( 'xo_functions_settings', array() );
    
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
        <p class="description"><?php esc_html_e( 'Toggle global modules across your production environment safely.', 'xo-functions' ); ?></p>
        <hr />

        <form method="post" action="options.php">
            <?php
            settings_fields( 'xo_functions_group' );
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