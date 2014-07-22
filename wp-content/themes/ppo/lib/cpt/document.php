<?php

// Document CPT
function document_cpt_init() {
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
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => false,
		'query_var' => true,
		'exclude_from_search' => false,
		'rewrite' => array( 'slug' => 'document/%document_type%', 'with_front' => FALSE ),
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
		'has_archive' => 'document',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'thumbnail' ),
		'taxonomies' => array( 'document_type' )
	);
	register_post_type( 'document', $document_args );
}

add_action( 'init', 'document_cpt_init' );

function create_document_taxonomies() {
	// Document Type
	$document_type_labels = array(
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
	$document_type_args = array(
		'hierarchical' => true,
		'labels' => $document_type_labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'document', 'with_front' => false ),
	);
	register_taxonomy( 'document_type', array( 'document' ), $document_type_args );

	// Death Type
	$death_type_labels = array(
		'name' => _x( 'Death Types', 'taxonomy general name' ),
		'singular_name' => _x( 'Death Type', 'taxonomy singular name' ),
		'search_items' => __( 'Search Death Types' ),
		'all_items' => __( 'All Death Types' ),
		'parent_item' => __( 'Parent Death Type' ),
		'parent_item_colon' => __( 'Parent Death Type:' ),
		'edit_item' => __( 'Edit Death Type' ),
		'update_item' => __( 'Update Death Type' ),
		'add_new_item' => __( 'Add New Death Type' ),
		'new_item_name' => __( 'New Death Type Name' ),
		'menu_name' => __( 'Death Types' ),
	);
	$death_type_args = array(
		'hierarchical' => true,
		'labels' => $death_type_labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'document', 'with_front' => false ),
	);
	register_taxonomy( 'fii-death-type', array( 'document' ), $death_type_args );

	// FII status
	$fii_status_labels = array(
		'name' => _x( 'FII Status', 'taxonomy general name' ),
		'singular_name' => _x( 'FII Status', 'taxonomy singular name' ),
		'search_items' => __( 'Search Statuses' ),
		'all_items' => __( 'All Statuses' ),
		'parent_item' => __( 'Parent Status' ),
		'parent_item_colon' => __( 'Parent Status:' ),
		'edit_item' => __( 'Edit Status' ),
		'update_item' => __( 'Update Status' ),
		'add_new_item' => __( 'Add New Status' ),
		'new_item_name' => __( 'New Status Name' ),
		'menu_name' => __( 'FII Status' ),
	);
	$fii_status_args = array(
		'hierarchical' => true,
		'labels' => $fii_status_labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'document', 'with_front' => false ),
	);
	register_taxonomy( 'fii-status', array( 'document' ), $fii_status_args );
}

add_action( 'init', 'create_document_taxonomies', 0 );

// Rename Featured Image metabox
function document_image_box() {
	remove_meta_box( 'postimagediv', 'document', 'side' );
	add_meta_box( 'postimagediv', __( 'Document thumbnail' ), 'post_thumbnail_meta_box', 'document', 'side', 'low' );
}

add_action( 'do_meta_boxes', 'document_image_box' );

// Replace "featured image" text in link in metabox
function document_featured_image_link( $content ) {
	global $post_type;
	if ( $post_type == 'document' ) {
		$content = str_replace( __( 'featured image' ), __( 'thumbnail' ), $content );
	}
	return $content;
}

add_filter( 'admin_post_thumbnail_html', 'document_featured_image_link' );

// Create thumbnail is thumbnail doesn't exist
function create_doc_thumbnail( $post_id ) {
	// Check post_type is document
	if ( get_post_type( $post_id ) == 'document' && !get_post_thumbnail_id( $post_id ) ) {
		//Get path of attachment
		$attachment_url = get_post_meta( $post_id, 'document-upload', true );
		$attachment_id = get_attachment_id_from_src( $attachment_url );
		$attachment_obj = get_post( $attachment_id );
		// Check to see if attachment is PDF
		if ( 'application/pdf' == get_post_mime_type( $attachment_obj ) ) {
			$attachment_path = get_attached_file( $attachment_id );

			//By adding [0] the first page gets selected, important because otherwise multi paged files wont't work
			$pdf_source = $attachment_path . '[0]';

			//Thumbnail format
			$tn_format = 'jpg';
			//Thumbnail output as path + format
			$thumb_out = str_replace( ".pdf", "", $attachment_path . '.' . $tn_format );
			//Thumbnail URL
			$thumb_url = str_replace( ".pdf", "", $attachment_url . '.' . $tn_format );

			//Setup various variables
			//Assuming A4 - portrait - 1.00x1.41
			$width = '159';
			$height = $width * 1.41;
			$quality = '90';
			$dpi = '300';

			//Create the thumbnail with choosen option
			$im = new imagick( $pdf_source );
			$im->setCompressionQuality( $quality );
			$im = $im->flattenImages();
			$im->setImageFormat( $tn_format );

			$new_height = $im->getImageheight();
			$new_width = $im->getImagewidth();
			if ( $new_width > $new_height ) {
				$im->cropImage( $new_width / 2, $new_height, $new_width / 2, 0 );
			}
			$im->scaleImage( $width, $height, true );

			$im->writeImage( $thumb_out );

			//Add thumbnail URL as metadata of pdf attachment
			$wp_filetype = wp_check_filetype( $thumb_out, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( basename( $thumb_out ) ),
				'post_content' => '',
				'post_status' => 'inherit',
				'guid' => $thumb_url
			);
			$attach_id = wp_insert_attachment( $attachment, $thumb_out, $post_id );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $thumb_out );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			set_post_thumbnail( $post_id, $attach_id );
		}
	}
}

add_action( 'save_post', 'create_doc_thumbnail' );

// Add thumbnail to admin view
// Add the column
function add_document_thumbnail_column( $cols ) {
	$colsstart = array_slice( $cols, 0, 1, true );
	$colsend = array_slice( $cols, 1, null, true );

	$cols = array_merge(
			$colsstart, array( 'doc_thumb' => __( '' ) ), $colsend
	);
	return $cols;
}

add_filter( 'manage_document_posts_columns', 'add_document_thumbnail_column', 5 );

// Grab featured-thumbnail size post thumbnail and display it.
function display_document_thumbnail_column( $col, $id ) {
	switch ( $col ) {
		case 'doc_thumb':
			if ( function_exists( 'the_post_thumbnail' ) ) {
				echo the_post_thumbnail( 'admin-list-thumb' );
			} else {
				echo 'Not supported in theme';
			}
			break;
	}
}

add_action( 'manage_document_posts_custom_column', 'display_document_thumbnail_column', 5, 2 );

// Change document_type permalink
function filter_post_type_link( $link, $post ) {
	if ( $post->post_type != 'document' ) {
		return $link;
	}
	if ( $cats = get_the_terms( $post->ID, 'document_type' ) ) {
		$link = str_replace( '%document_type%', array_pop( $cats )->slug, $link );
	}
	return $link;
}

add_filter( 'post_type_link', 'filter_post_type_link', 10, 2 );
