<?php
/**
 * Plugin Name: Mind Spirit Extensions
 * Plugin URI: http://wadellc.co
 * Description: Custom functions used to customize or support mindspiritcenter.org.
 * Author: David W. Couch
 * Author URI: http://wadellc.co
 * Version: 0.5.0
 */



/* 
 * Today's Date Shortcode
 * Simply returns today's date 
 * Usage: [todays_date] */
add_shortcode( 'todays_date', 'current_days_date' );
function current_days_date() {
    // Format: January 1, 2021
    return date("F j, Y");
}



/* 
 * Slug to Body class 
 */
function page_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = 'page-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'page_slug_body_class' );



/**
 * Enable shortcodes for menu navigation.
 * https://hoolite.be/wordpress/enable-wordpress-shortcodes-for-menu-and-widgets/
 */
if ( ! has_filter( 'wp_nav_menu', 'do_shortcode' ) ) {
    add_filter( 'wp_nav_menu', 'shortcode_unautop' );
    add_filter( 'wp_nav_menu', 'do_shortcode', 11 );
}



/**
 * Enable shortcodes for widgets (footer, sidebar...).
 * https://hoolite.be/wordpress/enable-wordpress-shortcodes-for-menu-and-widgets/
 */
if ( ! has_filter( 'widget_text', 'do_shortcode' ) ) {
    add_filter( 'widget_text', 'shortcode_unautop');
    add_filter( 'widget_text', 'do_shortcode', 11);
}



/* Search Form Shortcode 
 * Thanks Jeff Starr
 * https://wp-mix.com/wordpress-shortcode-display-search-form/
 * TODO: Add params for placeholder(s)
 * Usage: [search_form]
 */

function display_search_form() {
    $onfocus="this.placeholder='What can we help you find?'";
    $onblur="this.placeholder='Search...'";
    $search_form = '<form method="get" id="search-form-shortcode" action="'. esc_url(home_url('/')) .'">
        <input type="text" name="s" id="s" placeholder="Search..." onfocus="'.$onfocus.'" onblur="'.$onblur.'">
    </form>';
    return $search_form;
}
add_shortcode('search_form', 'display_search_form');

/* Search Results - Replace ?s= with 'search' slug and /result
 * https://www.wpbeginner.com/wp-tutorials/how-to-change-the-default-search-url-slug-in-wordpress/
 */
function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
        exit();
    }
}
add_action( 'template_redirect', 'wpb_change_search_url' );



/* 
 * Admin Column for Thumbnails 
 */
function add_thumbnail_to_post_list_data($column,$post_id){
    switch ($column)
    {
        case 'post_thumbnail':
            echo '<a href="' . get_edit_post_link() . '">'.the_post_thumbnail( 'thumbnail' ).'</a>';
            break;
    }
}

function add_thumbnail_to_post_list( $columns ){
    $columns['post_thumbnail'] = 'Thumbnail';
    return $columns;
}

// https://developer.wordpress.org/reference/functions/add_theme_support/
if (function_exists('add_theme_support')){
    // Add to 'posts'
    add_filter( 'manage_posts_columns' , 'add_thumbnail_to_post_list' );
    add_action( 'manage_posts_custom_column' , 'add_thumbnail_to_post_list_data', 10, 2 );
 
    // Add To Pages
    add_filter( 'manage_pages_columns' , 'add_thumbnail_to_post_list' );
    add_action( 'manage_pages_custom_column' , 'add_thumbnail_to_post_list_data', 10, 2 );
}





/* 
 * Third Party Support & Overides
 */

/* * * * * * * * * * * * 
 * SearchWP Customizations
 * https://searchwp.com/documentation/knowledge-base/creating-searchwp-customizations-plugin/
 * * * * * * * * * * * * */

/* SearchWP Algorithm */

/*
 * Force partial matches in SearchWP even when matches are found using provided search.
 * * * * */
    add_filter( 'searchwp\query\partial_matches\force', function( $force, $args ) {
      return true;
    }, 10, 2 );


/*
 * Give team-members extraordinary weight boost to ensure team-members show up first.
 * @link https://searchwp.com/documentation/knowledge-base/post-type-first-top/
 * * * * */
    add_filter( 'searchwp\query\mods', function( $mods ) {

      $post_type = 'team-member'; // Post type name.

      $source = \SearchWP\Utils::get_post_type_source_name( $post_type );

      $mod = new \SearchWP\Mod( $source );
      $mod->relevance( function( $runtime ) use ( $source ) {
        global $wpdb;

        return $wpdb->prepare(
            "IF( {$runtime->get_foreign_alias()}.source = %s, '999999999999', '0' )",
            $source
        );
      } );

      $mods[] = $mod;

      return $mods;
    } );


/* 
 * Add search weight to more recently published entries in SearchWP.
 * Weight decays over time and eventually will not add bonus weight.
 * @link https://searchwp.com/documentation/knowledge-base/add-relevance-weight-date/
 * * * * * */
    add_filter( 'searchwp\query\mods', function( $mods ) {
        global $wpdb;

        $weight_adjust = 15;

        $mod = new \SearchWP\Mod();
        $mod->set_local_table( $wpdb->posts );
        $mod->on( 'ID', [ 'column' => 'id' ] );
        $mod->relevance( function( $runtime_mod ) use ( $weight_adjust ) {
            $alias = $runtime_mod->get_local_table_alias();
            return "
            ( 100 * EXP(
                ( 1 - ABS( (
                    UNIX_TIMESTAMP( {$alias}.post_date )
                    - UNIX_TIMESTAMP( NOW() )
                ) / 86400 ) ) / 100 )
            * {$weight_adjust} )";
        } );
        $mods[] = $mod;

        return $mods;
    } );









/*
 * Group SearchWP results by Source, sort by relevance within each Source group.
 * @link https://searchwp.com/documentation/knowledge-base/group-results-by-source-post-type/
 * * * * * */
    add_filter( 'searchwp\query\mods', function( $mods, $query ) {
        $mod = new \SearchWP\Mod();
        $mod->order_by( function( $mod ) {
            // Search results should be grouped by Sources in this order.
            // NOTE: _ALL_ Engine Sources must be included here!
            $source_order = [
                //'user',
                \SearchWP\Utils::get_post_type_source_name( 'team-member' ),
                \SearchWP\Utils::get_post_type_source_name( 'page' ),
                \SearchWP\Utils::get_post_type_source_name( 'post' ),
            ];

            return "FIELD({$mod->get_foreign_alias()}.source, "
                . implode( ',', array_filter( array_map( function( $source_name ) {
                    global $wpdb;

                    return $wpdb->prepare( '%s', $source_name );
                }, $source_order ) ) ) . ')';
        }, '', 1 );

        $mods[] = $mod;

        return $mods;
    }, 10, 2 );




/* SearchWP UX */

    /*
     * Suggested Search Notification.
     * Override SearchWP's "Did you mean?" output. 
     * "We couldn't find a match to your search term. Here are results for XXXXX."
     * * * * */
    class MySearchwpDidYouMean {
        private $query;

        function __construct() {
            // Prevent SearchWP's automatic "Did you mean?" output.
            add_filter( 'searchwp\query\output_suggested_search', '__return_false' );

            // Grab the "Did you mean?" arguments to use later.
            add_action( 'searchwp\query\ran', function( $query ) {
                $this->query = $query;
            } );

            // Output custom "Did you mean?" message at the top of The Loop.
            add_action( 'loop_start', function( $wp_query ) {
                if ( empty( $this->query ) || ! $this->query->get_suggested_search() || ! $wp_query->is_main_query() ) {
                    return '';
                }

                $phrase_query = str_replace( array( '”', '“' ), '"', $this->query->get_keywords() );
                echo '<p class="searchwp-revised-search-notice">';
                echo wp_kses(
                    sprintf(
                    // Translators: First placeholder is the quoted search string. Second placeholder is the search string without quotes.
                        __( 'We couldn&apos;t find a match to your search term: <em class="searchwp-revised-search-original">%s</em>. Here are results for <em class="searchwp-suggested-revision-query">%s</em>.', 'searchwp' ),
                        esc_html( $phrase_query ),
                        esc_html( str_replace( '"', '', $this->query->get_suggested_search() ) )
                    ),
                    array(
                        'em' => array(
                            'class' => array(),
                        ),
                    )
                );
                echo '</p>';
            } );
        }
    }

    new MySearchwpDidYouMean();











/* Facet WP does not play nice with other queries */

/** Make sure all queries to be used with FacetWP
 ** have 'facetwp' => true in query args, including the
 ** query args setting in a facetwp template
 **/

// add 'facetwp' => false anytime it is not already set
add_action( 'pre_get_posts', function( $query ) {
    if ( ! isset( $query->query_var['facetwp'] ) ) {
        $query->set( 'facetwp', false );
    }
    return $query;
});

// use 'facetwp' query arg to determine main query
add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
    if ( isset( $query->query_vars['facetwp'] ) ) {
        $is_main_query = (bool) $query->query_vars['facetwp'];
    }
    return $is_main_query;
}, 10, 2 );

// fSelect place holder
add_filter( 'facetwp_render_output', function( $output ) {
    $output['settings']['mscc_specialty']['searchText'] = ' Enter Search Term';
    $output['settings']['mscc_therapies']['searchText'] = ' Enter Search Term';
    return $output;
});




/* 
 * Genesis Blocks Pro Plugin Overides 
 * 
 */

// Remove 'Genesis Blocks Pro' Portfolio CPT ~Phil Johnston
function disable_gbp_portfolio_post_type() {
    remove_action( 'init', 'Genesis\PageBuilder\Portfolio\register_portfolio_post_type' );
}
add_action( 'init', 'disable_gbp_portfolio_post_type', 9 );




/* 
 * Genesis Block Theme Overrides + Augmentations
 * 
 */




/* Menus */
/* Register additional Menus - Locations*/
function exo_register_nav_menu(){
        register_nav_menus( array(
            'utility_menu' => __( 'Utility Menu', 'mscc' ),
            'auxillary_menu'  => __( 'Auxillary Menu', 'mscc' ),
            //'main_menu'  => __( 'Main Menu', 'mscc' ),
            'mobile_menu'  => __( 'Mobile Menu', 'mscc' ),
        ) );
    }

add_action( 'after_setup_theme', 'exo_register_nav_menu', 0 );


/* Add-Ons */

// YouTube Embed Override - hide recommended videos at end of video
//require_once('youtube-embed.php');



?>