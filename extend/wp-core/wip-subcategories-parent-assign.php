<?php
/**
 * Feature: Assign Category to Parent (WIP)
 * Description: Adds parent category selector to quick edit for hierarchical taxonomies.
 * Part of: WordPress Core Utilities
 * Status: WORK IN PROGRESS - Not production ready
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register custom hidden structural column for custom Quick Editor hooks.
 */
add_filter( 'manage_edit-category_columns', 'xo_register_invisible_quickedit_column' );
add_filter( 'manage_edit-product_cat_columns', 'xo_register_invisible_quickedit_column' );
function xo_register_invisible_quickedit_column( $columns ) {
    $columns['xo_parent_placeholder'] = '';
    return $columns;
}

/**
 * Inject Parent Category Selector row into quick edit.
 */
add_action( 'quick_edit_custom_box', function( $column_name, $screen, $taxonomy ) {
    if ( 'edit-tags' !== $screen || 'xo_parent_placeholder' !== $column_name ) {
        return;
    }

    $taxonomy_object = get_taxonomy( $taxonomy );
    if ( ! $taxonomy_object || ! $taxonomy_object->hierarchical ) {
        return;
    }
    ?>
    <fieldset>
        <div class="inline-edit-col">
            <label>
                <span class="title"><?php esc_html_e( 'Parent', 'xo-functions' ); ?></span>
                <?php
                wp_dropdown_categories( array(
                    'taxonomy'         => $taxonomy,
                    'hide_empty'       => 0,
                    'name'             => 'xo_term_parent',
                    'id'               => 'xo_term_parent',
                    'show_option_none' => __( '(None)', 'xo-functions' ),
                    'class'            => 'xo-parent-drop',
                ) );
                ?>
            </label>
        </div>
    </fieldset>
    <?php
}, 10, 3 );

/**
 * Save parent category assignment from quick edit.
 *
 * FIX: This was previously an anonymous closure using __FUNCTION__ to remove
 * itself from the hook. Inside a closure, __FUNCTION__ returns the literal
 * string '{closure}', so remove_action() never matched — risking infinite
 * re-entry if wp_update_term() triggers edited_term again.
 *
 * Fixed by converting to a named function so remove_action() works correctly.
 */
function xo_save_quick_edit_parent( $term_id, $tt_id, $taxonomy ) {
    // Bail if this isn't a quick-edit tax save request.
    if ( ! isset( $_POST['action'] ) || 'inline-save-tax' !== $_POST['action'] ) {
        return;
    }

    // Verify the nonce WordPress generates for inline-save-tax requests.
    // WP core sends '_inline_edit' on the inline-save-tax AJAX action.
    if (
        ! isset( $_POST['_inline_edit'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_inline_edit'] ) ), 'taxinlineeditnonce' )
    ) {
        error_log( '[XO-FUNCTIONS ERROR] xo_save_quick_edit_parent: nonce verification failed for term ' . absint( $term_id ) );
        return;
    }

    if ( ! current_user_can( 'edit_term', $term_id ) ) {
        error_log( '[XO-FUNCTIONS ERROR] xo_save_quick_edit_parent: capability check failed for user on term ' . absint( $term_id ) );
        return;
    }

    $new_parent_id = -1;

    if ( isset( $_POST['xo_term_parent'] ) ) {
        $new_parent_id = absint( $_POST['xo_term_parent'] );
    } elseif ( isset( $_POST['taxonomy'], $_POST[ 'xo_term_parent_' . $_POST['taxonomy'] ] ) ) {
        $new_parent_id = absint( $_POST[ 'xo_term_parent_' . sanitize_key( $_POST['taxonomy'] ) ] );
    }

    if ( $new_parent_id < 0 ) {
        return;
    }

    // Guard against circular hierarchy.
    if ( $new_parent_id === (int) $term_id || term_is_ancestor_of( $term_id, $new_parent_id, $taxonomy ) ) {
        error_log( '[XO-FUNCTIONS ERROR] xo_save_quick_edit_parent: circular hierarchy detected for term ' . absint( $term_id ) . ', parent candidate ' . $new_parent_id );
        return;
    }

    // Remove self before calling wp_update_term() to prevent re-entrant hook execution.
    remove_action( 'edited_term', 'xo_save_quick_edit_parent', 10 );

    try {
        $result = wp_update_term( $term_id, $taxonomy, array( 'parent' => $new_parent_id ) );

        if ( is_wp_error( $result ) ) {
            error_log( '[XO-FUNCTIONS ERROR] xo_save_quick_edit_parent: wp_update_term failed — ' . $result->get_error_message() );
        }
    } catch ( \Throwable $e ) {
        error_log( '[XO-FUNCTIONS ERROR] xo_save_quick_edit_parent: exception — ' . $e->getMessage() );
    }
}
add_action( 'edited_term', 'xo_save_quick_edit_parent', 10, 3 );

/**
 * Admin UI tweaks for parent assignment.
 */
function xo_dynamic_admin_js_parent_assign() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || $screen->base !== 'edit-tags' ) {
        return;
    }

    $taxonomy_object = get_taxonomy( $screen->taxonomy );
    if ( ! $taxonomy_object ) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#the-list').on('click', 'a.editinline', function() {
                var rowData = $(this).closest('tr');
                var termId = rowData.attr('id').replace('tag-', '');

                setTimeout(function() {
                    var parentValue = $('#inline_' + termId + ' .parent').text();
                    var quickEditRow = $('#edit-' + termId);

                    if ( quickEditRow.length && parentValue !== undefined ) {
                        var selectedParent = parseInt(parentValue) || 0;
                        quickEditRow.find('#xo_term_parent').val(selectedParent);
                    }
                }, 50);
            });
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'xo_dynamic_admin_js_parent_assign' );
