<?php

// Document CPT
function cpt_init() {
	$document_labels = array(
		'name' => 'Documents',
		'singular_name' => 'Document',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Document',
		'edit_item' => 'Edit Document',
		'new_item' => 'New Document',
		'all_items' => 'All Documents',
		'view_item' => 'View Document',
		'search_items' => 'Search Documents',
		'not_found' => 'No document found',
		'not_found_in_trash' => 'No document found in Trash',
		'parent_item_colon' => '',
		'menu_name' => 'Documents'
	);
	$document_args = array(
		'labels' => $document_labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => false,
		'query_var' => false,
		'exclude_from_search' => true,
		'rewrite' => false,
		'capabilities' => array(
			'publish_posts' => 'delete_others_posts',
			'edit_posts' => 'delete_others_posts',
			'edit_others_posts' => 'delete_others_posts',
			'delete_posts' => 'delete_others_posts',
			'delete_others_posts' => 'delete_others_posts',
			'read_private_posts' => 'delete_others_posts',
			'edit_post' => 'delete_others_posts',
			'delete_post' => 'delete_others_posts',
			'read_post' => 'delete_others_posts'
		),
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title' )
	);
	register_post_type( 'document', $document_args );
}

add_action( 'init', 'cpt_init' );

function create_document_taxonomies() {
	$labels = array(
		'name' => _x( 'Document Types', 'taxonomy general name' ),
		'singular_name' => _x( 'Document Type', 'taxonomy singular name' ),
		'search_items' => __( 'Search Document Types' ),
		'all_items' => __( 'All Document Types' ),
		'parent_item' => __( 'Parent Document Type' ),
		'parent_item_colon' => __( 'Parent Document Type:' ),
		'edit_item' => __( 'Edit Document Type' ),
		'update_item' => __( 'Update Document Type' ),
		'add_new_item' => __( 'Add New Document Type' ),
		'new_item_name' => __( 'New Document Type Name' ),
		'menu_name' => __( 'Document Types' ),
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'doc-type' ),
	);

	register_taxonomy( 'document_type', array( 'document' ), $args );
}

add_action( 'init', 'create_document_taxonomies', 0 );
