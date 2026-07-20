<?php
/* Custom Post Type: Spotlight Module
 * Description: A shortcode to be used throughout the site to highlight select excerpts
 */


/* Initially built as custom fields 
 * Opted for CPT for performance and flexibility.
 * Spotlight CPT will have no Posts of it's own. However it is a collection of excerpts from internal and some external sources.
 *
 */
add_action( 'init', 'guppe_spotlight_post_type' );
function guppe_spotlight_post_type() {
	$slm_cpt_labels = [
		'name'                     => esc_html__( 'Stories from the Frontlines', 'guppe' ), //admin apge title
		'singular_name'            => esc_html__( 'Spotlight', 'guppe' ),
		'add_new'                  => esc_html__( 'Add Spotlight', 'guppe' ),
		'add_new_item'             => esc_html__( 'Add Spotlight', 'guppe' ),
		'edit_item'                => esc_html__( 'Edit Spotlight', 'guppe' ),
		'new_item'                 => esc_html__( 'New Spotlight', 'guppe' ),
		'menu_name'                => esc_html__( 'Spotlights', 'guppe' ), // admin menu

	];
	$args = [
		'label'               => esc_html__( 'Spotlight', 'guppe' ),
		'labels'              => $slm_cpt_labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => true,
		'exclude_from_search' => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'query_var'           => false,
		'can_export'          => true,
		'delete_with_user'    => false,
		'register_meta_box_cb' => 'ExcerptAsQuote',
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-megaphone',
		'capability_type'     => 'post',
		'supports'            => ['title', 'author',],
		'taxonomies'          => ['spotlight-category'],
		'rewrite'             => false,
	];

	register_post_type( 'guppe_spotlight', $args );
}



/*
 * Metabox build of Spotlight Categories for use with Spotlight Module / From the Front Lines.
 */

add_action( 'init', 'guppe_register_spotlight_categories' );
function guppe_register_spotlight_categories() {
	$slm_tax_labels = [
		'name'                       => esc_html__( 'Spotlight Categories', 'guppe' ),
		'singular_name'              => esc_html__( 'Spotlight Category', 'guppe' ),
		'menu_name'                  => esc_html__( 'Spotlight Categories', 'guppe' ),
		'search_items'               => esc_html__( 'Search Spotlight Categories', 'guppe' ),
		'popular_items'              => esc_html__( 'Popular Spotlight Categories', 'guppe' ),
		'all_items'                  => esc_html__( 'All Spotlight Categories', 'guppe' ),
		'parent_item'                => esc_html__( 'Parent Spotlight Category', 'guppe' ),
		'parent_item_colon'          => esc_html__( 'Parent Spotlight Category', 'guppe' ),
		'edit_item'                  => esc_html__( 'Edit Spotlight Category', 'guppe' ),
		'view_item'                  => esc_html__( 'View Spotlight Category', 'guppe' ),
		'update_item'                => esc_html__( 'Update Spotlight Category', 'guppe' ),
		'add_new_item'               => esc_html__( 'Add new spotlight category', 'guppe' ),
		'new_item_name'              => esc_html__( 'New spotlight category name', 'guppe' ),
		'separate_items_with_commas' => esc_html__( 'Separate spotlight categories with commas', 'guppe' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove spotlight categories', 'guppe' ),
		'choose_from_most_used'      => esc_html__( 'Choose most used spotlight categories', 'guppe' ),
		'not_found'                  => esc_html__( 'No spotlight categories found', 'guppe' ),
		'no_terms'                   => esc_html__( 'No spotlight categories found', 'guppe' ),
		'items_list_navigation'      => esc_html__( 'Spotlight categories list pagination', 'guppe' ),
		'items_list'                 => esc_html__( 'Spotlight Categories list', 'guppe' ),
		'most_used'                  => esc_html__( 'Most Used', 'guppe' ),
		'back_to_items'              => esc_html__( 'Back to spotlight categories', 'guppe' ),
		'text_domain'                => esc_html__( 'guppe', 'guppe' ),
	];
	$args = [
		'label'              => esc_html__( 'Spotlight Categories', 'guppe' ),
		'labels'             => $slm_tax_labels,
		'description'        => 'Spotlight Categories are based on themes that enable logical placement of spotlight modules on website pages to present user with related posts. Themes follow patterns in blog categories but are not exactly the same. They are people-based and more granular to avoid replicating the same quote in different sections of a page that has many Spotlight modules.',
		'public'             => true,
		'publicly_queryable' => false,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_rest'       => true,
		'show_tagcloud'      => flase,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'query_var'          => true,
		'sort'               => true,
		'rest_base'          => '',
		'rewrite'            => [
			'with_front'   => false,
			'hierarchical' => false,
		],
	];
	register_taxonomy( 'spotlight-category', ['guppe_spotlight'], $args );
}

/* MetaBox Demo repeater */




/* 
* MetaBox build of custom fields for supporting the spotlight module 
*/

add_filter( 'rwmb_meta_boxes', 'guppe_register_spotlight_metaboxes' );

function guppe_register_spotlight_metaboxes( $meta_boxes ) {
    $prefix = 'guppe_slm_';

    $meta_boxes[] = [
        'title'   => esc_html__( 'Story', 'guppe' ),
        'post_types' => 'guppe_spotlight',
        'id'      => 'slm_container',
        'autosave' => true,
        'context' => 'normal',
        'fields'  => [
			/*[
                'type' => 'custom_html',
                'std'  => '<div>Stories from the frontlines. Each story is called a spotlight.</div>',
            ],
            [
                'id'                => $prefix . 'spotlight_group',
                'type'              => 'group',
                'collapsible' 		=> true,
                'default_state'		=> 'collapsed',
                'group_title' 		=> 'Spotlight {#}',
                // 'group_title' 		=> 'Spotlight {#}: {guppe_slm-quote}', // too long
                // 'group_title' 		=> 'Spotlight {#}: ' . substr('guppe_slm-quote',0,10).'...', // Cannot Trim Array Value String
                'clone'             => true,
                'sort_clone'        => true,
                'clone_as_multiple' => true,
                'add_button'        => __( 'Add another Spotlight', 'guppe' ),

                'fields'            => [*/
		            [
		                'type' => 'textarea',
		                'name' => esc_html__( 'Quote', 'guppe' ),
		                'id'   => $prefix . 'quote',
		                'desc' => esc_html__( 'Quotation marks should not be included.', 'guppe' ),
		                'rows' => 4,
		            ],
		            [
		            	'type' => 'heading',
    					'name' => 'Attribution Group',
    					'desc' => 'If these fields are left blank, it is assumed this is not a quote from the article, but a snippet of key text dynamically attributed to blog post author followed by, "Get Us PPE"',
		            ],
		            [
		                'type' => 'text',
		                'name' => esc_html__( 'Name', 'guppe' ),
		                'id'   => $prefix . 'name',
		                'desc' => esc_html__( 'Can be \'Anonymous\' or occupation-based (School Nurse)', 'guppe' ),
		            ],
		            [
		                'type' => 'text',
		                'name' => esc_html__( 'Organization', 'guppe' ),
		                'id'   => $prefix . 'organization',
		            ],
		            [
		                'type' => 'text',
		                'name' => esc_html__( 'City', 'guppe' ),
		                'id'   => $prefix . 'city',
		            ],
		            [
		                'type' => 'text',
		                'name' => esc_html__( 'State', 'guppe' ),
		                'id'   => $prefix . 'state',
		                'desc' => esc_html__( 'Two letter U.S. states. But longer names for Territory or Country allowed.', 'guppe' ),
		            ],
		            [
		            	'type' => 'heading',
    					'name' => 'Source Group',
    					'desc' => 'You may add a custom title to either an internal or external url. This will serve as the link in the bottom of the spotlight."',
		            ],
		            [
					    'name'        => 'Blog Post of origin',
					    'id'          => $prefix . 'post',
					    'type'        => 'post',
					    'desc'		=> esc_html('Select internal post or add external URL below'),

					    // Post type.
					    'post_type'   => 'post',

					    // Field type.
					    'field_type'  => 'select_advanced',

					    // Placeholder, inherited from `select_advanced` field.
					    'placeholder' => 'Select a blog post',

					    // Query arguments. See https://codex.wordpress.org/Class_Reference/WP_Query
					    'query_args'  => array(
					        'post_status'    => 'publish',
					        'posts_per_page' => - 1,
					        'orderby'			=> 'name',
					        'order'				=> 'ASC',
					    ),
		            ],
		            [
		                'type' => 'text',
		                'name' => esc_html__( 'Custom Title', 'guppe' ),
		                'id'   => $prefix . 'working_title',
		                'desc' => esc_html__( 'If left blank, the title of the selected post will be used.', 'guppe' ),
		            ],
		            [
					 	'name' => 'URL',
					 	'id' => $prefix . 'url',
					 	'type' => 'url',
					 	'desc' => esc_html('Paste external URL here.'),
					 ],

                //],
            //],
        ],
    ];

    return $meta_boxes;
}

?>