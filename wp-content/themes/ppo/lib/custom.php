<?php

/**
 * Custom functions
 */
/* Add footer menu */
function create_footer_menu() {
	register_nav_menu( 'footer-navigation', "Footer Menu" );
}

add_action( 'init', 'create_footer_menu' );

/* Setup option tree */
add_filter( 'ot_theme_mode', '__return_true' );
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_use_theme_options', '__return_false' );

//load_template( trailingslashit( get_template_directory() ) . 'inc/theme-options.php' );
require_once (trailingslashit( get_template_directory() ) . 'option-tree/ot-loader.php');

/**
 * Meta Boxes
 */
load_template( trailingslashit( get_template_directory() ) . 'lib/meta-boxes.php' );

/* Add excerpts to pages */
add_action( 'init', 'my_add_excerpts_to_pages' );

function my_add_excerpts_to_pages() {
	add_post_type_support( 'page', 'excerpt' );
}
