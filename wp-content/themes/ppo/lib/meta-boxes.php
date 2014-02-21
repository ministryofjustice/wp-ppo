<?php

/**
 * Initialize the meta boxes. 
 */
add_action( 'admin_init', 'custom_meta_boxes' );

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
						array( 'id' => 'link', 'label' => 'Linked content', 'type' => 'custom_post_type_select', 'post_type' => 'page,post' )
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
			'slug' => array('complaints-faq','fatal-incident-faqs'),
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
		)
	);

	$admin_post_id = (isset( $_GET['post'] ) ? $_GET['post'] : 0);

//	var_dump($_POST);

	foreach ( $my_meta_boxes as $meta_box ) {
		// Hacky way to stop meta-box appearing on other pages, yet still be processed when submitted
		$post_details = get_post( $admin_post_id );
		if (
				(isset( $meta_box['slug'] ) && ($post_details->post_name == $meta_box['slug']) || in_array($post_details->post_name,$meta_box['slug'])) ||
				!isset( $meta_box['slug'] ) || isset( $_POST['_wpnonce'] )
		) {
			ot_register_meta_box( $meta_box );
		}
	}
}
