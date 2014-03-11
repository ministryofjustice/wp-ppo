<?php

/**
 * Custom functions
 */
/* Change OT datepicker format */
function change_ot_date_format() {
	return "dd/mm/yy";
}

add_filter( 'ot_type_date_picker_date_format', 'change_ot_date_format', 20 );

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

/* Get attachment ID from URL */

function get_attachment_id_from_src( $image_src ) {
	global $wpdb;
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	$id = $wpdb->get_var( $query );
	return $id;
}

/* Returns friendly filesize */

function file_size_convert( $bytes ) {
	$bytes = floatval( $bytes );
	$arBytes = array(
		0 => array( "UNIT" => "TB", "VALUE" => pow( 1024, 4 ) ),
		1 => array( "UNIT" => "GB", "VALUE" => pow( 1024, 3 ) ),
		2 => array( "UNIT" => "MB", "VALUE" => pow( 1024, 2 ) ),
		3 => array( "UNIT" => "KB", "VALUE" => 1024 ),
		4 => array( "UNIT" => "B", "VALUE" => 1 ),
	);

	foreach ( $arBytes as $arItem ) {
		if ( $bytes >= $arItem["VALUE"] ) {
			$result = $bytes / $arItem["VALUE"];
			$result = strval( round( $result, 2 ) ) . " " . $arItem["UNIT"];
			break;
		}
	}
	return $result;
}

/* Returns file size */

function get_filesize( $file_url, $brackets = false ) {
	$attachment_id = get_attachment_id_from_src( $file_url );
	$local_path = get_attached_file( $attachment_id );
//	return ($brackets ? "(" : "") . file_size_convert( filesize( $local_path ) ) . ($brackets ? ")" : "");
	return $file_url;
}
