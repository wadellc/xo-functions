<?php
/**
 * Feature: Dynamic Taxonomies in Menus
 * Description: Dynamically inject WooCommerce Brands/Taxonomies with active inventory into the menu.
 * Part of: WordPress Core Utilities
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * a. Register the custom control section (meta box) in nav-menus.php
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
 * b. Render the checklist UI inside the menu sidebar panel with exposed system slugs
 */
function render_dynamic_taxonomies_menu_meta_box() {
// Fetch all public taxonomies
    $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
    
    // 1. Remove native WordPress internal structural elements
    unset( $taxonomies['nav_menu'], $taxonomies['link_category'], $taxonomies['post_format'] );
    
    // 2. Remove standard operational core parameters you don't need in menus
    unset( $taxonomies['product_visibility'], $taxonomies['product_shipping_class'] );

    // 3. Loop through and strip out all WooCommerce product attributes ('pa_color', etc.) and Swatch plugins
    foreach ( $taxonomies as $slug => $tax_object ) {
        // Exclude WooCommerce core attributes
        if ( strpos( $slug, 'pa_' ) === 0 ) {
            unset( $taxonomies[$slug] );
        }
        
        // Exclude Swatches options / variations taxonomies if they match plugin strings
        if ( strpos( $slug, 'swatch' ) !== false || strpos( $slug, 'wvs_' ) === 0 ) {
            unset( $taxonomies[$slug] );
        }
    }
    ?>
    <div id="dynamic-taxonomies-box" class="taxonomydiv">
        <div class="tabs-panel tabs-panel-active" style="max-height: 300px; overflow-y: auto;">
            <ul class="categorychecklist form-no-clear">
                <?php 
                foreach ( $taxonomies as $tax ) : 
                    // Verify if this specific taxonomy has at least ONE term with items assigned to it
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
 * c. Inject native menu manipulation handler JS
 */
add_action( 'admin_print_footer_scripts-nav-menus.php', 'dynamic_taxonomies_menu_admin_scripts' );
function dynamic_taxonomies_menu_admin_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#submit-dynamic-taxonomies').on('click', function(e) {
            e.preventDefault();
            
            var $box = $('#dynamic-taxonomies-box');
            var $checked = $box.find('input[type="checkbox"]:checked');
            var $spinner = $box.find('.spinner');
            
            if ($checked.length === 0) {
                return;
            }
            
            $spinner.addClass('is-active');
            
            var items = {};
            $checked.each(function(index, el) {
                var taxSlug = $(el).val();
                var taxLabel = $(el).data('label');
                
                items[index] = {
                    'menu-item-type': 'custom',
                    'menu-item-url': '#',
                    'menu-item-title': 'Dynamic ' + taxLabel,
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
 * d. Intercept Front-end Menu Render Engine: Inject Terms on-the-fly
 */
add_filter( 'wp_get_nav_menu_items', 'process_dynamic_taxonomies_frontend', 10, 3 );
function process_dynamic_taxonomies_frontend( $items, $menu, $args ) {
    if ( is_admin() ) {
        return $items;
    }

    $updated_items = array();
    $menu_order = 0;

    foreach ( $items as $item ) {
        $menu_order++;
        $item->menu_order = $menu_order;
        $updated_items[] = $item;

        if ( ! empty( $item->classes ) && is_array( $item->classes ) && in_array( 'dynamic-taxonomy', $item->classes ) ) {
            
            $taxonomy = '';
            foreach ( $item->classes as $class ) {
                if ( strpos( $class, 'tax-' ) === 0 ) {
                    $taxonomy = str_replace( 'tax-', '', $class );
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

            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                foreach ( $terms as $term ) {
                    
                    // WooCommerce Inventory Verification Loop Guard
                    if ( class_exists( 'WooCommerce' ) && ( $taxonomy === 'product_cat' || $taxonomy === 'product_brand' ) ) {
                        if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
                            
                            $has_stock = get_posts( array(
                                'post_type'      => 'product',
                                'posts_per_page' => 1,
                                'fields'         => 'ids',
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => $taxonomy,
                                        'field'    => 'term_id',
                                        'terms'    => $term->term_id,
                                    ),
                                ),
                                'meta_query'     => array(
                                    array(
                                        'key'     => '_stock_status',
                                        'value'   => 'instock',
                                        'compare' => '=',
                                    ),
                                ),
                            ) );
                            
                            if ( empty( $has_stock ) ) {
                                continue;
                            }
                        }
                    }

                    $menu_order++;
                    $fake_id = 3000000 + $term->term_id;

                    $term_item = (object) array(
                        'ID'                => $fake_id,
                        'db_id'             => $fake_id,
                        'title'             => $term->name,
                        'url'               => get_term_link( $term ),
                        'menu_item_parent'  => $item->ID,
                        'object_id'         => $term->term_id,
                        'object'            => $taxonomy,
                        'type'              => 'taxonomy',
                        'classes'           => array( 'menu-item', 'menu-item-type-taxonomy', 'menu-item-object-' . $taxonomy, 'dynamic-term-dropdown' ),
                        'target'            => '',
                        'attr_title'        => '',
                        'description'       => '',
                        'xfn'               => '',
                        'status'            => 'publish',
                        'menu_order'        => $menu_order,
                    );

                    $updated_items[] = $term_item;
                }
            }
        }
    }

    return $updated_items;
}
