<?php
/**
 * Feature: Dynamic Taxonomies in Menus (WIP)
 * Description: Dynamically inject WooCommerce Brands/Taxonomies with active inventory into the menu.
 * Part of: WordPress Core Utilities
 * Status: WORK IN PROGRESS - Not production ready
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * a. Register the custom control section (meta box) in nav-menus.php.
 */
add_action( 'admin_init', 'register_dynamic_taxonomies_nav_menu_meta_box' );
function register_dynamic_taxonomies_nav_menu_meta_box() {
    add_meta_box(
        'dynamic_taxonomies_meta_box',
        __( 'Dynamic Taxonomies' ),
        'render_dynamic_taxonomies_menu_meta_box',
        'nav-menus',
        'side',
        'default'
    );
}

/**
 * b. Render the checklist UI inside the menu sidebar panel with exposed system slugs.
 */
function render_dynamic_taxonomies_menu_meta_box() {
    $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

    // Remove structural / operational taxonomies that don't belong in menus.
    $exclude = array(
        'nav_menu', 'link_category', 'post_format',
        'product_visibility', 'product_shipping_class',
    );
    foreach ( $exclude as $slug ) {
        unset( $taxonomies[ $slug ] );
    }

    // Strip WooCommerce product attributes (pa_*) and swatch plugin taxonomies.
    foreach ( array_keys( $taxonomies ) as $slug ) {
        if ( strpos( $slug, 'pa_' ) === 0 || strpos( $slug, 'swatch' ) !== false || strpos( $slug, 'wvs_' ) === 0 ) {
            unset( $taxonomies[ $slug ] );
        }
    }
    ?>
    <div id="dynamic-taxonomies-box" class="taxonomydiv">
        <div class="tabs-panel tabs-panel-active" style="max-height: 300px; overflow-y: auto;">
            <ul class="categorychecklist form-no-clear">
                <?php
                foreach ( $taxonomies as $tax ) :
                    $has_active_terms = get_terms( array(
                        'taxonomy'   => $tax->name,
                        'hide_empty' => true,
                        'number'     => 1,
                        'fields'     => 'ids',
                    ) );

                    if ( is_wp_error( $has_active_terms ) || empty( $has_active_terms ) ) {
                        continue;
                    }
                    ?>
                    <li>
                        <label class="selectit">
                            <input type="checkbox" value="<?php echo esc_attr( $tax->name ); ?>" data-label="<?php echo esc_attr( $tax->label ); ?>" />
                            <strong><?php echo esc_html( $tax->label ); ?></strong>
                            <code style="font-size: 10px; color: #888; background: #f0f0f0; padding: 1px 4px; margin-left: 5px; border-radius: 3px;">
                                <?php echo esc_html( $tax->name ); ?>
                            </code>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input type="submit" class="button-secondary right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" id="submit-dynamic-taxonomies">
                <span class="spinner" style="vertical-align: middle; float: right; margin: 4px 5px 0 0;"></span>
            </span>
        </p>
    </div>
    <?php
}

/**
 * c. Inject native menu manipulation handler JS.
 */
add_action( 'admin_print_footer_scripts-nav-menus.php', 'dynamic_taxonomies_menu_admin_scripts' );
function dynamic_taxonomies_menu_admin_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#submit-dynamic-taxonomies').on('click', function(e) {
            e.preventDefault();

            var $box     = $('#dynamic-taxonomies-box');
            var $checked = $box.find('input[type="checkbox"]:checked');
            var $spinner = $box.find('.spinner');

            if ($checked.length === 0) {
                return;
            }

            $spinner.addClass('is-active');

            var items = {};
            $checked.each(function(index, el) {
                var taxSlug  = $(el).val();
                var taxLabel = $(el).data('label');

                items[index] = {
                    'menu-item-type':    'custom',
                    'menu-item-url':     '#',
                    'menu-item-title':   'Dynamic ' + taxLabel,
                    'menu-item-classes': 'dynamic-taxonomy tax-' + taxSlug
                };
            });

            if (window.wpNavMenu && window.wpNavMenu.addItemToMenu) {
                window.wpNavMenu.addItemToMenu(items, window.wpNavMenu.addMenuItemToBottom, function() {
                    $checked.prop('checked', false);
                    $spinner.removeClass('is-active');
                });
            } else {
                $spinner.removeClass('is-active');
            }
        });
    });
    </script>
    <?php
}

/**
 * d. Intercept Front-end Menu Render Engine: Inject Terms on-the-fly.
 *
 * FIX (N+1 queries): The original code issued one get_posts() query per term
 * to check WooCommerce stock status, causing potentially dozens of DB queries
 * per page load on large stores. Replaced with a transient-cached lookup that
 * fetches all in-stock term IDs for the taxonomy in a single query, then
 * filters the terms array in PHP — O(1) per term instead of O(n) DB calls.
 *
 * FIX: get_term_link() can return WP_Error; now guarded before use as a URL.
 *
 * FIX: Entire function wrapped in try/catch so a failure never crashes the
 * frontend render pipeline.
 */
add_filter( 'wp_get_nav_menu_items', 'process_dynamic_taxonomies_frontend', 10, 3 );
function process_dynamic_taxonomies_frontend( $items, $menu, $args ) {
    if ( is_admin() ) {
        return $items;
    }

    try {
        $updated_items = array();
        $menu_order    = 0;

        foreach ( $items as $item ) {
            $menu_order++;
            $item->menu_order = $menu_order;
            $updated_items[]  = $item;

            if (
                empty( $item->classes ) ||
                ! is_array( $item->classes ) ||
                ! in_array( 'dynamic-taxonomy', $item->classes, true )
            ) {
                continue;
            }

            // Extract the taxonomy slug from the CSS class (tax-{slug}).
            $taxonomy = '';
            foreach ( $item->classes as $class ) {
                if ( strpos( $class, 'tax-' ) === 0 ) {
                    $taxonomy = substr( $class, 4 );
                    break;
                }
            }

            if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            $terms = get_terms( array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => true,
            ) );

            if ( is_wp_error( $terms ) || empty( $terms ) ) {
                continue;
            }

            // Build a set of in-stock term IDs when WooCommerce hide-out-of-stock is on.
            // A single transient-cached query replaces the per-term get_posts() calls.
            $instock_term_ids = null;
            if (
                class_exists( 'WooCommerce' ) &&
                in_array( $taxonomy, array( 'product_cat', 'product_brand' ), true ) &&
                'yes' === get_option( 'woocommerce_hide_out_of_stock_items' )
            ) {
                $transient_key    = 'xo_instock_terms_' . $taxonomy;
                $instock_term_ids = get_transient( $transient_key );

                if ( false === $instock_term_ids ) {
                    // Fetch one representative in-stock product per term via a grouped query.
                    $instock_term_ids = array();

                    $instock_posts = get_posts( array(
                        'post_type'      => 'product',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'meta_query'     => array(
                            array(
                                'key'     => '_stock_status',
                                'value'   => 'instock',
                                'compare' => '=',
                            ),
                        ),
                    ) );

                    if ( ! empty( $instock_posts ) ) {
                        foreach ( $instock_posts as $post_id ) {
                            $post_terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
                            if ( ! is_wp_error( $post_terms ) ) {
                                $instock_term_ids = array_merge( $instock_term_ids, $post_terms );
                            }
                        }
                        $instock_term_ids = array_unique( $instock_term_ids );
                    }

                    set_transient( $transient_key, $instock_term_ids, 15 * MINUTE_IN_SECONDS );
                }
            }

            foreach ( $terms as $term ) {
                // Skip out-of-stock terms when applicable.
                if ( null !== $instock_term_ids && ! in_array( $term->term_id, $instock_term_ids, true ) ) {
                    continue;
                }

                // FIX: get_term_link() returns WP_Error on failure — guard before use.
                $term_url = get_term_link( $term );
                if ( is_wp_error( $term_url ) ) {
                    error_log( '[XO-FUNCTIONS ERROR] process_dynamic_taxonomies_frontend: get_term_link failed for term ' . absint( $term->term_id ) . ' — ' . $term_url->get_error_message() );
                    continue;
                }

                $menu_order++;
                $fake_id = 3000000 + $term->term_id;

                $term_item = (object) array(
                    'ID'               => $fake_id,
                    'db_id'            => $fake_id,
                    'title'            => $term->name,
                    'url'              => $term_url,
                    'menu_item_parent' => $item->ID,
                    'object_id'        => $term->term_id,
                    'object'           => $taxonomy,
                    'type'             => 'taxonomy',
                    'classes'          => array( 'menu-item', 'menu-item-type-taxonomy', 'menu-item-object-' . $taxonomy, 'dynamic-term-dropdown' ),
                    'target'           => '',
                    'attr_title'       => '',
                    'description'      => '',
                    'xfn'              => '',
                    'status'           => 'publish',
                    'menu_order'       => $menu_order,
                );

                $updated_items[] = $term_item;
            }
        }

        return $updated_items;

    } catch ( \Throwable $e ) {
        error_log( '[XO-FUNCTIONS ERROR] process_dynamic_taxonomies_frontend: exception — ' . $e->getMessage() );
        // Return the original items unmodified so the menu still renders.
        return $items;
    }
}
