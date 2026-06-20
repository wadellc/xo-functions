<?php
/**
 * Dynamic Subcategory Tree Optimization Tools.
 *
 * Adds clean "Add Sub-[Taxonomy]" deep linking parameters to hierarchical
 * taxonomies and automatically isolates view context when sub-branches are managed.
 *
 * @package    XO_Functions
 * @subpackage Core_Utilities
 * @category   Taxonomies
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.1.7
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Localized "Add Sub-[Taxonomy]" Action Row Insertion
 * //ToDo: Fix plural of Categoy. Not Categorys. Maybe use the existing labels to pull the plural form instead of hardcoding "Sub-[Singular]"?
 * Appends custom deep links into standard action layouts for hierarchical tables.
 */
function xo_dynamic_subcategory_row_action( $actions, $tag ) {
    $taxonomy_object = get_taxonomy( $tag->taxonomy );

    if ( $taxonomy_object && $taxonomy_object->hierarchical ) {
        // FIX: $singular_name was previously unescaped in the link text — XSS vector
        // from a custom taxonomy label. Always run through esc_html().
        $singular_name = esc_html( ucwords( $taxonomy_object->labels->singular_name ) );
        $subcat_url    = admin_url(
            'edit-tags.php?taxonomy=' . esc_attr( $tag->taxonomy )
            . '&prefill_parent=' . absint( $tag->term_id )
            . '&master_parent=' . absint( $tag->term_id )
        );
        /* translators: %s: taxonomy singular name, e.g. "Category" */
        $link_text = sprintf( esc_html__( 'Add Sub-%s', 'xo-functions' ), $singular_name );

        $actions['add_subcat'] = '<a href="' . esc_url( $subcat_url ) . '">' . $link_text . '</a>';
    }

    return $actions;
}
add_filter( 'tag_row_actions', 'xo_dynamic_subcategory_row_action', 10, 2 );

/**
 * 2. Term Query Isolator Filter
 *
 * Dynamically limits the viewport to parent and children sets when working in deep branches.
 */
function xo_isolate_taxonomy_list_by_parent( $obj ) {
    if ( ! is_admin() || ! isset( $_GET['master_parent'] ) ) {
        return;
    }
    if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
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

        if ( is_array( $children ) && ! is_wp_error( $children ) ) {
            $obj->query_vars['include'] = array_merge( array( $master_id ), $children );
        } else {
            $obj->query_vars['include'] = array( $master_id );
        }
    }
}
add_action( 'parse_term_query', 'xo_isolate_taxonomy_list_by_parent' );

/**
 * 3. Script-Injected Dashboard UI Tweaks
 *
 * Runs background structural updates on elements during deep branch modifications.
 */
function xo_dynamic_admin_js_subcategories() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || $screen->base !== 'edit-tags' ) {
        return;
    }

    $taxonomy_object = get_taxonomy( $screen->taxonomy );
    if ( ! $taxonomy_object ) {
        return;
    }

    $singular_label   = $taxonomy_object->labels->singular_name;
    $parent_id        = isset( $_GET['prefill_parent'] ) ? absint( $_GET['prefill_parent'] ) : 0;
    $master_parent_id = isset( $_GET['master_parent'] ) ? absint( $_GET['master_parent'] ) : 0;

    $parent_term_name = '';
    if ( $master_parent_id > 0 ) {
        $term = get_term( $master_parent_id, $screen->taxonomy );
        if ( $term && ! is_wp_error( $term ) ) {
            $parent_term_name = $term->name;
        }
    }

    $reset_url = admin_url( 'edit-tags.php?taxonomy=' . esc_attr( $screen->taxonomy ) );

    if ( $parent_id > 0 || $master_parent_id > 0 ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var parentDropdown = $('#parent');
                var submitButton   = $('#submit');
                var nameField      = $('#tag-name');
                var formHeading    = $('#col-left h2');

                var parentId       = <?php echo absint( $parent_id ); ?>;
                var masterParentId = <?php echo absint( $master_parent_id ); ?>;

                if ( parentId > 0 && parentDropdown.length ) {
                    parentDropdown.val(parentId).trigger('change');
                }

                if ( parentId > 0 ) {
                    var dynamicText = "<?php printf( esc_html__( 'Add New Sub-%s', 'xo-functions' ), esc_js( ucwords( $singular_label ) ) ); ?>";
                    if ( submitButton.length ) {
                        submitButton.val( dynamicText );
                    }
                    if ( formHeading.length ) {
                        formHeading.text( dynamicText );
                    }
                }

                if ( masterParentId > 0 ) {
                    var heading = $('.wp-heading-inline');
                    if ( heading.length ) {
                        heading.after(' <a href="<?php echo esc_url( $reset_url ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'xo-functions' ); ?></a>' );

                        var parentName   = "<?php echo esc_js( sanitize_text_field( $parent_term_name ) ); ?>";
                        var taxLabel     = "<?php echo esc_js( ucwords( $singular_label ) ); ?>s";
                        var subtitleText = parentName + " and Sub-" + taxLabel;

                        $('.wp-header-end').before('<p class="description custom-tax-subtitle" style="font-size: 14px; margin: 8px 0 15px 0; color: #646970; font-style: italic;">' + subtitleText + '</p>');
                    }
                }

                if ( nameField.length ) {
                    nameField.focus();
                }
            });
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'xo_dynamic_admin_js_subcategories' );
