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
		) // faq-meta-box
	);

	$admin_post_id = (isset( $_GET['post'] ) ? $_GET['post'] : 0);

//	var_dump($_POST);

	if ( is_edit_page() ) {

		foreach ( $my_meta_boxes as $meta_box ) {
			// Hacky way to stop meta-box appearing on other pages, yet still be processed when submitted
			$post_details = get_post( $admin_post_id );
			if ( isset( $meta_box['slug'] ) ) {
				if (
						($post_details->post_name == $meta_box['slug']) ||
						(is_array( $meta_box['slug'] ) &&
						in_array( $post_details->post_name, $meta_box['slug'] ) ) ||
						!isset( $meta_box['slug'] ) || isset( $_POST['_wpnonce'] )
				) {
					ot_register_meta_box( $meta_box );
				}
			}
		}
	}
}

function is_edit_page( $new_edit = null ) {
	global $pagenow;
	//make sure we are on the backend
	if ( !is_admin() )
		return false;


	if ( $new_edit == "edit" )
		return in_array( $pagenow, array( 'post.php', ) );
	elseif ( $new_edit == "new" ) //check for new post page
		return in_array( $pagenow, array( 'post-new.php' ) );
	else //check for either new or edit
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}
