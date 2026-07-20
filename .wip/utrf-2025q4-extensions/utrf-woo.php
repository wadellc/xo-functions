<?php
/**
 * Woo Commerce modifications for UTRF
 * Adapted to this plugin September of 2021
 * 
 */

echo '<script>console.log("woo-top")</script>';

/* Migrated Woo Commerce functions from UTRF 2012 */
/* ** Decalare Woo Support for themes that don't support it already ** */
/*// Unhook
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Hook
add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<div id="primary" class="content-area col-md-8">';
}

function my_theme_wrapper_end() {
  echo '</div>';
}*/


// Hide Warning - We now support WooThemes
/*add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}*/

// deprecated in 7.2
//add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 24;' ), 20 );

function change_woo_cart_list_count() {
    return 24; //return any number, -1 === show all
};
add_filter('loop_shop_per_page', 'change_woo_cart_list_count', 10, 0);
/* End Woo */





/* Use plugin to overide Woo Templates.
 * https://www.skyverge.com/blog/override-woocommerce-template-file-within-a-plugin/
 *
 */

function myplugin_plugin_path() {
  // gets the absolute path to this plugin directory
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}


//add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );

function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {

  global $woocommerce;
  $_template = $template;
  if ( ! $template_path ) $template_path = $woocommerce->template_url;
  $plugin_path  = myplugin_plugin_path() . '/templates/woocommerce/';

  echo '<script>console.log("' . $plugin_path .'")</script>';
  echo '<script>console.log("' . $template .'")</script>';
  echo '<script>console.log("' . $template_name .'")</script>';
  echo '<script>console.log("' . $template_path .'")</script>';

  // Look within passed path within the theme - this is priority
  $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
  );

  // Modification: Get the template from this plugin, if it exists
  if ( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;

  // Use default template
  if ( ! $template )
    $template = $_template;

  // Return what we found
  return $template;
}










/**
 * Override default WooCommerce templates and template parts from plugin.
 * 
 * E.g.
 * Override template 'woocommerce/loop/result-count.php' with 'my-plugin/woocommerce/loop/result-count.php'.
 * Override template part 'woocommerce/content-product.php' with 'my-plugin/woocommerce/content-product.php'.
 *
 * Note: We used folder name 'woocommerce' in plugin to override all woocommerce templates and template parts.
 * You can change it as per your requirement.
 */

// Override Template Part's.
add_filter( 'wc_get_template_part',             'override_woocommerce_template_part', 10, 3 );

// Override Template's.
add_filter( 'woocommerce_locate_template',      'override_woocommerce_template', 10, 3 );

/**
 * Template Part's
 *
 * @param  string $template Default template file path.
 * @param  string $slug     Template file slug.
 * @param  string $name     Template file name.
 * @return string           Return the template part from plugin.
 */

function override_woocommerce_template_part( $template, $slug, $name ) {
    // UNCOMMENT FOR @DEBUGGING
    echo '<pre>';
    echo 'Template Part<br/>';
    echo 'template: ' . $template . '<br/>';
    echo 'slug: ' . $slug . '<br/>';
    echo 'name: ' . $name . '<br/>';
    echo '</pre>';
    // Template directory.
    // E.g. /wp-content/plugins/my-plugin/woocommerce/
    $template_directory = untrailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/woocommerce/';
    if ( $name ) {
        $path = $template_directory . "{$slug}-{$name}.php";
    } else {
        $path = $template_directory . "{$slug}.php";
    }
    return file_exists( $path ) ? $path : $template;
}


/**
 * Template File
 *
 * @param  string $template      Default template file  path.
 * @param  string $template_name Template file name.
 * @param  string $template_path Template file directory file path.
 * @return string                Return the template file from plugin.
 */
function override_woocommerce_template( $template, $template_name, $template_path ) {
    // UNCOMMENT FOR @DEBUGGING
    echo '<pre>';
    echo 'Template File<br/>';
    echo 'template: ' . $template . '<br/>';
    echo 'template_name: ' . $template_name . '<br/>';
    echo 'template_path: ' . $template_path . '<br/>';
    echo '</pre>';
    // Template directory.
    // E.g. /wp-content/plugins/my-plugin/woocommerce/
    $template_directory = untrailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/woocommerce/';
    $path = $template_directory . $template_name;
    return file_exists( $path ) ? $path : $template;
}