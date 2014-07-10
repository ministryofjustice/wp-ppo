<?php

/**
 * Initialize the meta boxes. 
 */
add_action( 'admin_init', 'custom_meta_boxes' );

function get_template_pages( $template_name ) {
	$faq_pages = get_pages( array(
		'meta_key' => '_wp_page_template',
		'meta_value' => 'template-' . $template_name . '.php',
		'hierarchical' => 0
			) );
	$faq_array = array();
	foreach ( $faq_pages as $faq_page ) {
		$faq_array[] = $faq_page->post_name;
	}
	return $faq_array;
}

function custom_meta_boxes() {

	// Array hold all meta-boxes - slug param is custom to control which page it appears on
	$my_meta_boxes = array(
		array(
			'slug' => 'home',
			'id' => 'home_meta_box',
			'title' => 'Home Page Options',
			'pages' => array( 'page' ),
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
					'id' => 'home-order',
					'label' => '"What\'s New" Sort order',
					'type' => 'select',
					'choices' => array(
						array( 'label' => 'Chronological', 'value' => 'abc' ),
						array( 'label' => 'Reverse chronological', 'value' => 'cba' ),
						array( 'label' => 'Manual', 'value' => 'manual' )
					)
				),
				array(
					'id' => 'whats-new-content',
					'label' => 'What\'s New',
					'type' => 'list-item',
					'desc' => 'Title is not displayed, but must exist',
					'settings' => array(
						array( 'id' => 'date', 'label' => 'Date', 'type' => 'date_picker' ),
						array( 'id' => 'content', 'label' => 'Content', 'desc' => 'May contain HTML tags', 'type' => 'textarea' )
					)
				),
				array(
					'id' => 'ppo-info-content',
					'label' => 'PPO Information',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'content', 'label' => 'Content', 'desc' => 'May contain HTML tags', 'type' => 'textarea' ),
						// TODO: Entry below allows pages or posts to be selected. If other relevant CPTs are created they should be added to the list
						// TODO: Maybe move the list of accepted post types or CPTs to a variable so it can be reused
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'page,post' )
					)
				),
				array(
					'id' => 'quick-links-content',
					'label' => 'Quick Links',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'content', 'label' => 'Content', 'desc' => 'May contain HTML tags', 'type' => 'textarea' ),
						// TODO: Entry below allows pages or posts to be selected. If other relevant CPTs are created they should be added to the list
						// TODO: Maybe move the list of accepted post types or CPTs to a variable so it can be reused
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'page,post' )
					)
				)
			)
		), //home_meta_box
		array(
			'id' => 'sidebar_meta',
			'disabled' => true,
			'title' => 'Sidebar Content',
			'pages' => array( 'page', 'post' ),
			'context' => 'normal',
			'priority' => 'default',
			'fields' => array(
				array(
					'id' => 'sidebar-related-docs',
					'label' => 'Related Docs',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'attachment' )
					// Note that this will be changed to appropriate CPT(s) when created
					)
				),
				array(
					'id' => 'sidebar-quick-links',
					'label' => 'Quick Links',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'page,post' )
					// Note that this will be changed to appropriate CPT(s) when created
					)
				),
				array(
					'id' => 'sidebar-see-also',
					'label' => 'See Also...',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'page,post' )
					// Note that this will be changed to appropriate CPT(s) when created
					)
				),
				array(
					'id' => 'sidebar-contact',
					'type' => 'text',
					'label' => 'Contact email address',
					'std' => 'mail@ppo.gsi.gov.uk',
					'desc' => 'Leave empty to just display non-linked text below'
				),
				array(
					'id' => 'sidebar-contact-text',
					'type' => 'text',
					'label' => 'Contact text',
					'std' => '',
					'desc' => 'If empty then widget will not display'
				)
			)
		), //sidebar_meta
		array(
			'slug' => get_template_pages( 'faq' ),
			'id' => 'faq-meta-box',
			'title' => 'Frequently Asked Questions',
			'pages' => array( 'page' ),
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
					'id' => 'faq-entries',
					'label' => 'FAQ Entries',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'answer', 'label' => 'Answer', 'type' => 'textarea' )
					)
				)
			)
		), // faq-meta-box
		array(
			'slug' => get_template_pages( 'filelist' ),
			'id' => 'filelist-meta-box',
			'title' => 'File List',
			'pages' => array( 'page' ),
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
					'id' => 'filelist-entries',
					'label' => 'Files',
					'type' => 'list-item',
					'settings' => array(
						array( 'id' => 'file', 'label' => 'File', 'type' => 'upload' ),
						array( 'id' => 'date', 'label' => 'Upload Date', 'type' => 'date-picker', 'std' => date( 'd/m/Y' ) )
					)
				)
			)
		), // faq-meta-box
		array(
			'id' => 'document-type-meta-box',
			'title' => 'Document type',
			'pages' => array( 'document' ),
			'context' => 'side',
			'priority' => 'default',
			'fields' => array(
				array(
					'id' => 'document-type',
					'label' => 'Document type',
					'type' => 'taxonomy-select',
					'taxonomy' => 'document_type'
				),
				array(
					'id' => 'document-date',
					'label' => 'Document date',
					'type' => 'date_picker'
				)
			)
		), // document-type-meta-box
		array(
			'id' => 'document-meta-box',
			'title' => 'Document upload',
			'pages' => array( 'document' ),
			'context' => 'normal',
			'priority' => 'default',
			'fields' => array(
				array(
					'id' => 'document-upload',
					'label' => 'Upload document',
					'type' => 'upload'
				),
				array(
					'id' => 'document-description',
					'label' => 'Description',
					'type' => 'textarea'
				)
			)
		), // document-meta-box
		array(
			'id' => 'document-fii-meta-box',
			'title' => 'FII Details',
			'pages' => 'document',
			'control' => array(
				array( 'taxonomy' => 'document_type', 'value' => 'fii-report' )
			),
			'context' => 'normal',
			'priority' => 'default',
			'fields' => array(
				array(
					'id' => 'fii-death-date',
					'label' => 'Date of death',
					'type' => 'date_picker'
				),
				array(
					'id' => 'fii-death-type',
					'label' => 'Type of death',
					'type' => 'taxonomy_select',
					'taxonomy' => 'fii-death-type'
				),
				array(
					'id' => 'fii-establishment',
					'label' => 'Establishment',
					'type' => 'custom_post_type_select',
					'post_type' => 'establishment'
				),
				array(
					'id' => 'fii-status',
					'label' => 'Status',
					'type' => 'taxonomy_select',
					'taxonomy' => 'fii-status'
				),
				array(
					'id' => 'fii-gender',
					'label' => 'Gender',
					'type' => 'select',
					'choices' => array(
						array( 'value' => 'm', 'label' => 'Male' ),
						array( 'value' => 'f', 'label' => 'Female' )
					)
				),
				array(
					'id' => 'fii-age',
					'label' => 'Age group',
					'type' => 'select',
					'choices' => array(
						array( 'value' => '18-21', 'label' => '18-21' ),
						array( 'value' => '22-30', 'label' => '22-30' ),
						array( 'value' => '31-40', 'label' => '31-40' ),
						array( 'value' => '41-50', 'label' => '41-50' ),
						array( 'value' => '51-60', 'label' => '51-60' ),
						array( 'value' => '61+', 'label' => '61+' ),
					)
				),
				array(
					'id' => 'fii-case-id',
					'label' => 'Case ID',
					'type' => 'text',
				),
			)
		),
		array(
			'id' => 'establishment-meta-box',
			'title' => 'Establishment Details',
			'pages' => 'establishment',
			'context' => 'normal',
			'priority' => 'default',
			'fields' => array(
				array(
					'id' => 'establishment-type',
					'label' => 'Establishment Type',
					'type' => 'taxonomy_select',
					'taxonomy' => 'establishment-type'
				)
			)
		)
	);

	$admin_post_id = (filter_input( INPUT_GET, 'post' ) ? filter_input( INPUT_GET, 'post' ) : 0);

	// Hacky way to stop meta-box appearing on other pages, yet still be processed when submitted
	// TODO: Refactor into seperate function (and possibly add to wp-util or branch option-tree and include there)
	// function filter_metabox($post_id,$metabox_array) {
	$post_details = get_post( $admin_post_id );
	$post_exists = isset( $post_details );

	if ( is_edit_page() ) {
		foreach ( $my_meta_boxes as $meta_box ) {
			if ( !isset( $meta_box['disabled'] ) || !$meta_box['disabled'] ) {
				$show_metabox = false;
				$has_slug = isset( $meta_box['slug'] );
				$has_control = isset( $meta_box['control'] );
				if ( !$has_control && !$has_slug || isset( $_POST['_wpnonce'] ) ) {
					$show_metabox = true;
				} elseif ( $post_exists ) {
					if ( $has_slug ) { // Controls visibility by slug
						if (
								$post_details->post_name === $meta_box['slug'] ||
								(is_array( $meta_box['slug'] ) &&
								in_array( $post_details->post_name, $meta_box['slug'] ) )
						) {
							$show_metabox = true;
						}
					} elseif ( $has_control ) { // Controls visibility by taxonomy (but need to save first)
						foreach ( $meta_box['control'] as $control ) {
							$post_taxonomy = wp_get_post_terms( $admin_post_id, $control['taxonomy'] );
							if ( $post_taxonomy && $post_taxonomy[0]->slug == $control['value'] ) {
								$show_metabox = true;
							}
						}
					}
				}
				// Show metabox 
				if ( $show_metabox ) {
					ot_register_meta_box( $meta_box );
				}
			}
		}
	}
}

function is_edit_page( $new_edit = null ) {
	global $pagenow;
	//make sure we are on the backend
	if ( !is_admin() ) {
		return false;
	}


	if ( $new_edit == "edit" ) {
		return in_array( $pagenow, array( 'post.php', ) );
	} elseif ( $new_edit == "new" ) { //check for new post page
		return in_array( $pagenow, array( 'post-new.php' ) );
	} else { //check for either new or edit
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}
}
