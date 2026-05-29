<?php
/**
 * Gravity Forms Utility Extensions.
 *
 * Optimizes administrative dashboards by filtering for active states by default,
 * and appends custom activity tracking data grids directly into core list views.
 *
 * @package    XO_Functions
 * @subpackage Gravity_Forms
 * @category   Dashboard_Filters
 * @author     David W. Couch <http://wadellc.co>
 * @version    2.0.0
 * @since      1.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. SET FORM LIST TO ACTIVE BY DEFAULT
 *
 * Intercepts Gravity Forms dashboard routing to hide inactive clutter automatically.
 */
add_action( 'admin_init', function() {
    // Only target the specific form list admin view
    if ( ! class_exists( 'GFForms' ) || ! method_exists( 'GFForms', 'get_page' ) || GFForms::get_page() !== 'form_list' ) {
        return;
    }

    // Safeguard: Ensure headers haven't already been fired by another extension
    if ( headers_sent() ) {
        return;
    }

    $params = array();

    // Only inject sorting parameters if none are explicitly set
    if ( ! isset( $_GET['sort'] ) && ! isset( $_GET['orderby'] ) ) {
        $params = array(
            'sort'    => 'id',
            'dir'     => 'desc',
            'orderby' => 'id',
            'order'   => 'desc',
        );
    }

    // Only inject filter if no view filter is active
    if ( ! isset( $_GET['filter'] ) ) {
        $params['filter'] = 'active';
    }

    // Fire the redirect only if changes are needed, preventing endless redirection loops
    if ( ! empty( $params ) ) {
        wp_redirect( add_query_arg( $params ) );
        exit;
    }
} );


/**
 * 2. CUSTOM FORM LIST COLUMNS
 *
 * Displays latest entry activity context metrics directly on the admin overview layout.
 */
add_filter( 'gform_form_list_columns', function( $columns ) {
    $columns['latest_entry'] = esc_html__( 'Latest Entry Date', 'xo-functions' );
    return $columns;
} );

add_action( 'gform_form_list_column_latest_entry', function( $item ) {
    $form_id = absint( $item->id );

    // Verify API availability safely
    if ( ! class_exists( 'GFAPI' ) ) {
        return;
    }

    $search_criteria = array( 'status' => 'active' );
    $sorting         = array( 'key' => 'date_created', 'direction' => 'DESC' );
    $paging          = array( 'offset' => 0, 'page_size' => 1 );

    $entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

    if ( ! empty( $entries ) && isset( $entries[0]['date_created'] ) ) {
        $date_string = $entries[0]['date_created'];
        
        // Output site-localized and formatted layout values safely matching site settings
        $timestamp      = strtotime( $date_string );
        $date_format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        $formatted_date = function_exists( 'wp_date' ) ? wp_date( $date_format, $timestamp ) : date_i18n( $date_format, $timestamp );
        
        echo esc_html( $formatted_date );
    } else {
        echo '<span style="color:#999;">' . esc_html__( 'No entries yet', 'xo-functions' ) . '</span>';
    }
} );