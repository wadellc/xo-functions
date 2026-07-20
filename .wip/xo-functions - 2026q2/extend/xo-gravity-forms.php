<?php
/**
 * Extend Gravity Forms
 */
 // Only run this if Gravity Forms is active
if ( class_exists( 'GFCommon' ) ) {


/**
 * Gravity Wiz // Gravity Forms // Default Form List to Active Forms
 *
 * Sets Form List view to Active Forms by Default.
 *
 * @version 0.1
 * @author  David Smith <david@gravitywiz.com>
 * @license GPL-2.0+
 * @link    https://gravitywiz.com
 *
 * Plugin Name: Gravity Forms Default Form List to Active
 * Plugin URI: https://gravitywiz.com
 * Description: Sets Form List view to Active Forms by Default.
 * Author: Gravity Wiz
 * Version: 0.1
 * Author URI: https://gravitywiz.com
 *
 */
add_action( 'init', function() {
    if ( ! class_exists( 'GFForms' ) ) {
        return;
    }
    if ( GFForms::get_page() === 'form_list' ) {

        $params = array();

        if ( ! isset( $_GET['sort'] ) ) {
            $params = array(
                'sort'    => 'id',
                'dir'     => 'desc',
                'orderby' => 'id',
                'order'   => 'desc',
            );
        }

        if ( ! isset( $_GET['filter'] ) ) {
            $params['filter'] = 'active';
        }

        if ( ! empty( $params ) ) {
            wp_redirect( add_query_arg( $params ) );
            exit;
        }
    }

} );





/* Display Latest Entry Column
 * Displays latest entrt of each form in last column of forms admin
 */
    add_filter( 'gform_form_list_columns', 'add_latest_entry_column' );
    function add_latest_entry_column( $columns ) {
        $columns['latest_entry'] = 'Latest Entry Date';
        return $columns;
    }

    add_action( 'gform_form_list_column_latest_entry', 'populate_latest_entry_column' );
    function populate_latest_entry_column( $item ) {
        $form_id = $item->id;

        // Double-check the API class exists before calling it
        if ( ! class_exists( 'GFAPI' ) ) {
            return;
        }

        $search_criteria = array( 'status' => 'active' );
        $sorting         = array( 'key' => 'date_created', 'direction' => 'DESC' );
        $paging          = array( 'offset' => 0, 'page_size' => 1 );

        $entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

        if ( ! empty( $entries ) ) {
            $last_entry  = $entries[0];
            $date_string = $last_entry['date_created'];
            
            // Format using WordPress site settings
            echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $date_string ) );
        } else {
            echo '<span style="color:#999;">No entries yet</span>';
        }
    }
    //End Last Entry Column


}
