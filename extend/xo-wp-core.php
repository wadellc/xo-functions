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
 * 2. ADD SUBCATEGORIES
 * Add Row action to 'Add Subcategory' to Categories Taxonomy.
 */

/* Add capitalized custom "Add Sub-[Taxonomy]" link to hierarchical taxonomy rows
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

/* Backend Filter: Isolate the term list safely to prevent Fatal/Critical Errors
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

/* Frontend UI Filter: View All Link, Subtitle, Autofocus, Dropdown Select, and Text Renaming
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

            // Pre-select parent dropdown
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

            // Inject "View All" link AND the dynamic context subtitle
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

            // Autofocus the 'Name' input field for immediate keyboard entry
            if ( nameField.length ) {
                nameField.focus();
            }
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'custom_dynamic_admin_js_tweaks' );





/**
 * 3. PLUGIN INSTALL DATES
 * Groups activation logging, column handling, and layout styling together.
 */
// 3a. Capture the activation date when a plugin is first activated
add_action( 'activated_plugin', function( $plugin, $network_wide ) {
    $install_dates = get_option( 'plugin_install_dates', array() );
    
    if ( ! isset( $install_dates[$plugin] ) ) {
        $install_dates[$plugin] = current_time( 'mysql' );
        update_option( 'plugin_install_dates', $install_dates );
    }
}, 10, 2 );

// 3b. Add the "Install Date" column header to the Plugins table
add_filter( 'manage_plugins_columns', function( $columns ) {
    $columns['install_date'] = 'Install Date';
    return $columns;
});

// 3c. Populate the column with the saved date
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

// 3d. Inline CSS injection to format the table layout width
add_action( 'admin_head', function() {
    echo '<style>
        .column-install_date { 
            width: 120px !important; 
            white-space: nowrap;
        }
    </style>';
});
