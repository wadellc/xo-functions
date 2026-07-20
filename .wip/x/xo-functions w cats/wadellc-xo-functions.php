<?php
/**
 * Plugin Name: Exo-functions
 * Plugin URI: http://wadellc.co
 * Description: Custom functions used to customize and support WordPress sites. Optimized for Block Themes, Gravity Forms, and WooCommerce.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.8.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. TODAY'S DATE SHORTCODE
 * Usage: [todays_date] 
 * Respects WordPress Timezone settings.
 */
function exo_todays_date_shortcode() {
    // Returns format: March 29, 2026
    return current_time( 'F j, Y' );
}
add_shortcode( 'todays_date', 'exo_todays_date_shortcode' );


/**
 * 2. SLUG TO BODY CLASS
 * Appends the post/page slug to the body class for specific CSS targeting.
 */
function exo_page_slug_body_class( $classes ) {
    global $post;
    if ( is_singular() && isset( $post->post_name ) ) {
        $classes[] = sanitize_html_class( 'page-' . $post->post_name );
    }
    return $classes;
}
add_filter( 'body_class', 'exo_page_slug_body_class' );



/**
 * #. ADD SUBCATEGORIES
 * Add Row action to 'Add Subcategory' to Categories Taxonomy.
 */
/**
 * 1. Add capitalized custom "Add Sub-[Taxonomy]" link to hierarchical taxonomy rows
 */
function custom_dynamic_subcategory_row_action( $actions, $tag ) {
    $taxonomy_object = get_taxonomy( $tag->taxonomy );

    if ( $taxonomy_object && $taxonomy_object->hierarchical ) {
        $singular_name = $taxonomy_object->labels->singular_name;

        // Pass 'prefill_parent' for form manipulation and 'master_parent' to isolate the term list view
        $subcat_url = admin_url( 'edit-tags.php?taxonomy=' . esc_attr( $tag->taxonomy ) . '&prefill_parent=' . absint( $tag->term_id ) . '&master_parent=' . absint( $tag->term_id ) );
        
        $link_text = sprintf( esc_html__( 'Add Sub-%s', 'textdomain' ), ucwords( $singular_name ) );
        $actions['add_subcat'] = '<a href="' . esc_url( $subcat_url ) . '">' . $link_text . '</a>';
    }
    return $actions;
}
add_filter( 'tag_row_actions', 'custom_dynamic_subcategory_row_action', 10, 2 );

/**
 * 2. Backend Filter: Isolate the term list safely to prevent Fatal/Critical Errors
 */
function custom_isolate_taxonomy_list_by_parent( $obj ) {
    if ( ! is_admin() || ! isset( $_GET['master_parent'] ) ) {
        return;
    }

    if ( ! empty( $obj->query_vars['include'] ) ) {
        return;
    }

    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || $screen->base !== 'edit-tags' ) {
        return;
    }

    $master_id = absint( $_GET['master_parent'] );
    if ( $master_id > 0 ) {
        $children = get_term_children( $master_id, $screen->taxonomy );
        
        if ( is_array( $children ) ) {
            $obj->query_vars['include'] = array_merge( array( $master_id ), $children );
        } else {
            $obj->query_vars['include'] = array( $master_id );
        }
    }
}
add_action( 'parse_term_query', 'custom_isolate_taxonomy_list_by_parent' );

/**
 * 3. Frontend UI Filter: View All Link, Subtitle, Autofocus, Dropdown Select, and Text Renaming
 */
function custom_dynamic_admin_js_tweaks() {
    $screen = get_current_screen();
    
    if ( ! $screen || $screen->base !== 'edit-tags' ) {
        return;
    }

    $taxonomy_object = get_taxonomy( $screen->taxonomy );
    if ( ! $taxonomy_object ) {
        return;
    }

    $singular_label = $taxonomy_object->labels->singular_name;
    $parent_id      = isset( $_GET['prefill_parent'] ) ? absint( $_GET['prefill_parent'] ) : 0;
    
    // Retrieve the Parent Term Name dynamically for the subtitle
    $master_parent_id = isset( $_GET['master_parent'] ) ? absint( $_GET['master_parent'] ) : 0;
    $parent_term_name = '';
    if ( $master_parent_id > 0 ) {
        $term = get_term( $master_parent_id, $screen->taxonomy );
        if ( $term && ! is_wp_error( $term ) ) {
            $parent_term_name = $term->name;
        }
    }

    $reset_url = admin_url( 'edit-tags.php?taxonomy=' . esc_attr( $screen->taxonomy ) );
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var parentDropdown = $('#parent');
            var submitButton   = $('#submit');
            var nameField      = $('#tag-name');
            var formHeading    = $('#col-left h2');

            // 1. Pre-select parent dropdown
            if ( <?php echo $parent_id; ?> > 0 && parentDropdown.length ) {
                parentDropdown.val('<?php echo $parent_id; ?>').trigger('change');
            }

            // 2. Change the Form Submit Button Text & Form Title Heading
            if ( <?php echo $parent_id; ?> > 0 ) {
                var dynamicText = "<?php printf( esc_html__( 'Add New Sub-%s', 'textdomain' ), esc_js( ucwords( $singular_label ) ) ); ?>";
                
                if ( submitButton.length ) {
                    submitButton.val( dynamicText );
                }
                if ( formHeading.length ) {
                    formHeading.text( dynamicText );
                }
            }

            // 3. Inject "View All" link AND the dynamic context subtitle
            if ( <?php echo $master_parent_id > 0 ? 'true' : 'false'; ?> ) {
                var heading = $('.wp-heading-inline');
                if ( heading.length ) {
                    // Append the View All button
                    heading.after(' <a href="<?php echo esc_url( $reset_url ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'textdomain' ); ?></a>' );
                    
                    // Build the customized subtitle string
                    var parentName = "<?php echo esc_js( $parent_term_name ); ?>";
                    var taxLabel   = "<?php echo esc_js( ucwords( $singular_label ) ); ?>s"; // Adds 's' for plural representation
                    var subtitleText = parentName + " and Sub-" + taxLabel;
                    
                    // Inject the subtitle style container underneath the heading elements
                    $('.wp-header-end').before('<p class="description custom-tax-subtitle" style="font-size: 14px; margin: 8px 0 15px 0; color: #646970; font-style: italic;">' + subtitleText + '</p>');
                }
            }

            // 4. Autofocus the 'Name' input field for immediate keyboard entry
            if ( nameField.length ) {
                nameField.focus();
            }
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'custom_dynamic_admin_js_tweaks' );










/**
 * 3. ADMIN BAR TEMPLATE INFO
 * Displays the current template file in the admin bar for easier debugging.
 */
function exo_show_template_in_bar( $wp_admin_bar ) {
    if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
        global $template;
        $template_name = ( $template ) ? basename( $template ) : 'Unknown';
        
        $wp_admin_bar->add_node([
            'id'    => 'template-name',
            'title' => '<span class="ab-icon dashicons-layout"></span> Template: ' . esc_html( $template_name ),
        ]);
    }
}
add_action( 'admin_bar_menu', 'exo_show_template_in_bar', 999 );


/**
 * 4. PAGES ADMIN COLUMNS (Template Name)
 * Adds a column to the 'Pages' screen to see which template is assigned at a glance.
 */
add_filter( 'manage_pages_columns', function( $columns ) {
    $columns['exo_page_template'] = 'Template';
    return $columns;
});

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
 * 5. ENVIRONMENT CUE (Visual Border)
 * Injects a colored border based on the environment type.
 * Set WP_ENVIRONMENT_TYPE in wp-config.php: define( 'WP_ENVIRONMENT_TYPE', 'staging' );
 */
function exo_environment_admin_border() {
    $env = wp_get_environment_type(); // Default is 'production'
    
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
                content: '';
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                border-top: 3px solid $border_color;
                pointer-events: none;
                z-index: 999999;
                box-sizing: border-box;
            }
        </style>";
    }
}
add_action( 'admin_head', 'exo_environment_admin_border' );


/**
 * 5a. Theme Overrides Loader
 * Identifies the environment and loads site-specific logic.
 */
$current_theme = wp_get_theme()->get_template(); 
$current_host  = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';

/**
 * Wagner's Golf
 * https://wagnersgolfshop.com
 * Theme: Ona Pro
 */
if ( 'ona-pro' === $current_theme && strpos( $current_host, 'wagnersgolfshop' ) !== false ) {
    require_once plugin_dir_path( __FILE__ ) . 'mods/xo-mod-ona-shop-wagners.php';
}

/**
 * The Home Page
 * https://the-homepage.com
 * Theme: Twenty Twenty Five
 */
elseif ( 'twentytwentyfive' === $current_theme && strpos( $current_host, 'the-homepage' ) !== false ) {
    require_once plugin_dir_path( __FILE__ ) . 'mods/xo-mod-twentytwentyfive-the-homepage.php';
}

/**
 * Future Site
 */
// elseif ( ... ) { ... }





/**
 * 6. EXTENSION LOADER
 * Updated to look inside the PLUGIN folder instead of the THEME folder.
 */
function exo_load_plugin_extensions() {
    global $exo_active_exts;
    $exo_active_exts = array();

    // Look for /extend/ inside wp-content/plugins/exo-functions/
    $extend_path = plugin_dir_path( __FILE__ ) . 'extend/';
    
    $map = array(
        'xo-gravity-forms.php' => array( 'active' => class_exists( 'GFCommon' ),   'label' => 'Gravity Forms' ),
        'xo-woo-commerce.php'  => array( 'active' => class_exists( 'WooCommerce' ), 'label' => 'WooCommerce' ),
    );

    foreach ( $map as $file => $data ) {
        if ( $data['active'] && file_exists( $extend_path . $file ) ) {
            require_once $extend_path . $file;
            $exo_active_exts[] = $data['label'];
        }
    }
}
// Run this early so the global variable is ready for the UI
add_action( 'plugins_loaded', 'exo_load_plugin_extensions' );


/**
 * 7. PLUGIN UI - Updater
 * Uses 'plugin_row_meta' which is much more reliable than 'all_plugins'
 * for adding dynamic text like "Active Extensions."
 */
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file ) {
    global $exo_active_exts;
    
    // Check if this is our plugin row
    if ( $plugin_file === plugin_basename( __FILE__ ) ) {
        if ( ! empty( $exo_active_exts ) ) {
            $plugin_meta[] = '<span style="color:#2F4D2F;"><strong>Active Extensions:</strong> ' . esc_html( implode( ', ', $exo_active_exts ) ) . '</span>';
        } else {
            // Optional: Helps you debug if nothing is loading
            $plugin_meta[] = '<span style="color:#999;">No extensions loaded</span>';
        }
    }
    
    return $plugin_meta;
}, 10, 2 );


/**
 * 8. PLUGIN Admin UI - Activation Dates
 * Uses 'plugin_row_meta' which is much more reliable than 'all_plugins'
 * for adding dynamic text like "Active Extensions."
 */

// Capture the activation date when a plugin is first activated.

add_action('activated_plugin', 'save_plugin_activation_date', 10, 2);
function save_plugin_activation_date($plugin, $network_wide) {
    $install_dates = get_option('plugin_install_dates', array());
    
    // Only save if the date doesn't already exist to preserve the original date
    if (!isset($install_dates[$plugin])) {
        $install_dates[$plugin] = current_time('mysql');
        update_option('plugin_install_dates', $install_dates);
    }
}


// Add the "Install Date" column header to the Plugins table.

add_filter('manage_plugins_columns', 'add_plugin_install_date_column');
function add_plugin_install_date_column($columns) {
    $columns['install_date'] = 'Install Date';
    return $columns;
}


// Populate the column with the saved date.

add_action('manage_plugins_custom_column', 'show_plugin_install_date_column', 10, 3);
function show_plugin_install_date_column($column_name, $plugin_file, $plugin_data) {
    if ($column_name === 'install_date') {
        $install_dates = get_option('plugin_install_dates', array());
        
        if (isset($install_dates[$plugin_file])) {
            echo date_i18n(get_option('date_format'), strtotime($install_dates[$plugin_file]));
        } else {
            echo '<span style="color:#999;">Pre-existing</span>';
        }
    }
}


// Add CSS to widen the Install Date column.

add_action('admin_head', 'widen_plugin_install_column');
function widen_plugin_install_column() {
    echo '<style>
        .column-install_date { 
            width: 120px !important; 
            white-space: nowrap;
        }
    </style>';
}







