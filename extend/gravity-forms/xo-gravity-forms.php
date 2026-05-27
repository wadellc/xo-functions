<?php
/**
 * Extension Name: Gravity Forms Extensions
 * Description: Optimizes form list views, restricts defaults to active entries, and exposes submission tracking columns.
 * Part of: Exo-functions Global Utility Framework
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. SET FORM LIST TO ACTIVE BY DEFAULT
 * Intercepts Gravity Forms dashboard routing to hide inactive clutter automatically.
 */
add_action( 'admin_init', function() {
    // Only target the specific form list admin view
    if ( ! class_exists( 'GFForms' ) || ! method_exists( 'GFForms', 'get_page' ) || GFForms::get_page() !== 'form_list' ) {
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
 * Displays latest entry activity context metrics directly on the admin overview layout.
 */
add_filter( 'gform_form_list_columns', function( $columns ) {
    $columns['latest_entry'] = esc_html__( 'Latest Entry Date', 'textdomain' );
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
        
        // Output site localized and formatted layout values safely
        $formatted_date = date_i18n( 
            get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), 
            strtotime( $date_string ) 
        );
        echo esc_html( $formatted_date );
    } else {
        echo '<span style="color:#999;">' . esc_html__( 'No entries yet', 'textdomain' ) . '</span>';
    }
} );