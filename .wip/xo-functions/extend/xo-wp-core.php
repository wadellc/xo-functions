<?php
/**
 * Extension Name: WordPress Core Utilities
 * Description: Bundled core enhancements for administrative layout, page tracking, and debugging.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. SLUG TO BODY CLASS
 * Appends the post/page slug to the body class for specific CSS targeting.
 */
add_filter( 'body_class', function( $classes ) {
    global $post;
    if ( is_singular() && isset( $post->post_name ) ) {
        $classes[] = sanitize_html_class( 'page-' . $post->post_name );
    }
    return $classes;
});


/**
 * 2. ADMIN BAR TEMPLATE INFO
 * Displays the current template file in the admin bar for easier debugging.
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
        global $template;
        $template_name = ( $template ) ? basename( $template ) : 'Unknown';
        
        $wp_admin_bar->add_node([
            'id'    => 'template-name',
            'title' => '<span class="ab-icon dashicons-layout"></span> Template: ' . esc_html( $template_name ),
        ]);
    }
}, 999 );


/**
 * 3. PAGES ADMIN COLUMNS (Template Name)
 * Groups the header addition and the row builder layout logic together.
 */
// 3a. Add column header
add_filter( 'manage_pages_columns', function( $columns ) {
    $columns['exo_page_template'] = 'Template';
    return $columns;
});

// 3b. Populate column rows
add_action( 'manage_pages_custom_column', function( $column, $post_id ) {
    if ( $column === 'exo_page_template' ) {
        // Performance: Static cache to avoid reloading the theme object for every row
        static $theme_templates = null;
        if ( is_null( $theme_templates ) ) {
            $theme_templates = wp_get_theme()->get_page_templates();
        }

        $slug = get_page_template_slug( $post_id );
        
        if ( ! $slug || $slug === 'default' ) {
            echo '<span style="color:#999;">Default</span>';
        } else {
            $name = isset( $theme_templates[ $slug ] ) ? $theme_templates[ $slug ] : $slug;
            echo '<strong>' . esc_html( $name ) . '</strong>';
            echo '<br><small style="color:#666;">(' . esc_html( $slug ) . ')</small>';
        }
    }
}, 10, 2 );


/**
 * 4. PLUGIN INSTALL DATES
 * Groups activation logging, column handling, and layout styling together.
 */
// 4a. Capture the activation date when a plugin is first activated
add_action( 'activated_plugin', function( $plugin, $network_wide ) {
    $install_dates = get_option( 'plugin_install_dates', array() );
    
    if ( ! isset( $install_dates[$plugin] ) ) {
        $install_dates[$plugin] = current_time( 'mysql' );
        update_option( 'plugin_install_dates', $install_dates );
    }
}, 10, 2 );

// 4b. Add the "Install Date" column header to the Plugins table
add_filter( 'manage_plugins_columns', function( $columns ) {
    $columns['install_date'] = 'Install Date';
    return $columns;
});

// 4c. Populate the column with the saved date
add_action( 'manage_plugins_custom_column', function( $column_name, $plugin_file, $plugin_data ) {
    if ( $column_name === 'install_date' ) {
        $install_dates = get_option( 'plugin_install_dates', array() );
        
        if ( isset( $install_dates[$plugin_file] ) ) {
            echo date_i18n( get_option( 'date_format' ), strtotime( $install_dates[$plugin_file] ) );
        } else {
            echo '<span style="color:#999;">Pre-existing</span>';
        }
    }
}, 10, 3 );

// 4d. Inline CSS injection to format the table layout width
add_action( 'admin_head', function() {
    echo '<style>
        .column-install_date { 
            width: 120px !important; 
            white-space: nowrap;
        }
    </style>';
});
