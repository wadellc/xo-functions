<?php
/* 
 * Meta Box Build of Media Center CPT and Taxonomies
*/
add_action( 'init', 'guppe_register_media_center' );
function guppe_register_media_center() {
	$labels = [
		'name'                     => esc_html__( 'News', 'guppe' ),
		'singular_name'            => esc_html__( 'News', 'guppe' ),
		'add_new'                  => esc_html__( 'Add News', 'guppe' ),
		'add_new_item'             => esc_html__( 'Add News', 'guppe' ),
		'edit_item'                => esc_html__( 'Edit News', 'guppe' ),
		'new_item'                 => esc_html__( 'New News', 'guppe' ),
		'menu_name'                => esc_html__( 'Media Center', 'guppe' ),

	];
	$args = [
		'label'               => esc_html__( 'News', 'guppe' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => true,
		'has_archive'         => true, //media-center
		'rest_base'           => '',
		'register_meta_box_cb' => 'ExcerptDesc',
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-admin-site',
		'capability_type'     => 'post',
		'supports'            => ['title', 'thumbnail', 'excerpt'],
		'taxonomies'          => ['news-topic', 'media-type', 'spokesperson', 'news-outlet'],
		'rewrite'             => [
			'with_front' => false,
		],
	];

	register_post_type( 'news', $args );
}

/* Customize Excerpt Description for Media Center CPT */
function ExcerptDesc(){ add_filter( 'gettext', 'ExcerptDescription', 10, 2 );}

function ExcerptDescription( $translation, $original ){
    if ( 'Excerpt' == $original ) {
             return 'Citation'; //Change here to what you want Excerpt box to be called
        } else {
     		$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');         
            if ($pos !== false) {
                  return  'Populating the Excerpt field triggers this media to appear as Featured Media at top of the page (displays the most recent 5 entries).'; //Change the default text you see below the box with link to learn more...
            }
        } return $translation;
}




/* Metabox build of custom fields */
add_filter( 'rwmb_meta_boxes', 'guppe_metadata_for_news' );

function guppe_metadata_for_news( $meta_boxes ) {
    $prefix = 'mcnews';

    $meta_boxes[] = [
        'title'      => __( 'Media Coverage', 'guppe' ),
        'post_types' => ['news'],
        'context'    => 'after_title',
        'fields'     => [
            [
                'name' => __( 'Date of Original Post', 'guppe' ),
                'id'   => $prefix . 'date_of_original_post',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name'     => __( 'Link to Media Coverage', 'guppe' ),
                'id'       => $prefix . 'link_to_media_coverage',
                'type'     => 'url',
                'required' => true,
            ],
            [
                'type' => 'divider',
            ],
            [
                'type' => 'heading',
                'name' => __( 'Featured Media', 'guppe' ),
                'desc' => __( 'To appear at top of page, Featured Media requires <b>Excerpt</b> and <b>Featured Image</b>.', 'your-text-domain' ),
            ],
            [
                'name' => __( 'Photo Credit', 'guppe' ),
                'id'   => $prefix . 'photo_credit',
                'type' => 'text',
                'label_description' => __( '', 'guppe' ),
                'desc'              => __( 'if applicable', 'guppe' ),
                'placeholder'       => __( '', 'guppe' ),
                //'size'              => 22,
            ],
            [
                'name' => __( 'Photo Credit Link', 'guppe' ),
                'id'   => $prefix . 'photo_credit_link',
                'type' => 'url',
                'desc'              => __( 'if applicable', 'guppe' ),
            ],

        ],
    ];

    return $meta_boxes;
}

/* functions to display fields */
// echo rwmb_meta( 'mcnewsdate_of_original_post' );
// echo rwmb_meta( 'mcnewslink_to_media_coverage' );
// echo rwmb_meta( 'mcnewsphoto_credit' );
// echo rwmb_meta( 'mcnewsphoto_credit_link' );



/* Taxonomies:
	news-outlet
	spokesperson
	media-type
	news-topic
	*/
			

			/* Meta Box News Outlet Tax */
			add_action( 'init', 'guppe_register_news_outlet', 0 );
			function guppe_register_news_outlet() {
				$labels = [
					'name'                       => esc_html__( 'News Outlets', 'guppe' ),
					'singular_name'              => esc_html__( 'News Outlet', 'guppe' ),
					'menu_name'                  => esc_html__( 'News Outlets', 'guppe' ),
					'add_new_item'                    => esc_html__( 'Add News Outlet', 'guppe' ),
				];
				$args = [
					'label'              => esc_html__( 'Outlets', 'guppe' ),
					'labels'             => $labels,
					'description'        => '',
					'public'             => true,
					'publicly_queryable' => true,
					'hierarchical'       => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_nav_menus'  => true,
					'show_in_quick_edit' => true,
					'show_admin_column'  => true,
					'query_var'          => true,
				];
				register_taxonomy( 'news-outlet', [ 'news' ], $args );
			}

			

			

			/* Meta Box Spokespeople Tax */
			add_action( 'init', 'guppe_register_spokespeople', 0 );
			function guppe_register_spokespeople() {
				$labels = [
					'name'                       => esc_html__( 'Spokespeople', 'guppe' ),
					'singular_name'              => esc_html__( 'Spokesperson', 'guppe' ),
					'menu_name'                  => esc_html__( 'Spokespeople', 'guppe' ),
					'add_new_item'                    => esc_html__( 'Add Spokesperson', 'guppe' ),
				];
				$args = [
					'label'              => esc_html__( 'Spokespeople', 'guppe' ),
					'labels'             => $labels,
					'description'        => '',
					'public'             => true,
					'publicly_queryable' => true,
					'hierarchical'       => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_nav_menus'  => true,
					'show_in_quick_edit' => true,
					'show_admin_column'  => true,
					'query_var'          => true,

				];
				//register_taxonomy( 'spokesperson', ['post'], $args );
				register_taxonomy( 'spokesperson', [ 'news' ], $args );
			}




			/* Meta Box build of Media Type Taxonomy */
			add_action( 'init', 'guppe_register_media_type', 0 );
			function guppe_register_media_type() {
				$labels = [
					'name'                       => esc_html__( 'Media Types', 'guppe' ),
					'singular_name'              => esc_html__( 'Media Type', 'guppe' ),
					'menu_name'                  => esc_html__( 'Media Types', 'guppe' ),
					'add_new_item'                  	 => esc_html__( 'Add Media Type', 'guppe' ),

				];
				$args = [
					'label'              => esc_html__( 'Media Types', 'guppe' ),
					'labels'             => $labels,
					'description'        => 'Print & Digital, Broadcast or Radio',
					'public'             => true,
					'hierarchical'       => true,
					'show_ui'            => true,
					'show_in_nav_menus'  => true,
					'show_in_quick_edit' => true,
					'show_admin_column'  => true,
					'query_var'          => true,

				];
				register_taxonomy( 'media-type', [ 'news' ], $args );
			}






			/* Meta Box build of News Topics Taxonomy */
			add_action( 'init', 'guppe_register_taxonomy', 0 );
			function guppe_register_taxonomy() {
				$labels = [
					'name'                       => esc_html__( 'News Topics', 'guppe' ),
					'singular_name'              => esc_html__( 'News Topic', 'guppe' ),
					'menu_name'                  => esc_html__( 'News Topics', 'guppe' ),
					'add_new_item'                  	 => esc_html__( 'Add new Topic', 'guppe' ),
				];
				$args = [
					'label'              => esc_html__( 'News Topics', 'guppe' ),
					'labels'             => $labels,
					'public'             => true,
					'hierarchical'       => true,
					'show_ui'            => true,
					'show_in_nav_menus'  => true,
					'show_in_quick_edit' => true,
					'show_admin_column'  => true,
					'query_var'          => true,

				];
				register_taxonomy( 'news-topic', [ 'news' ], $args );
			}

