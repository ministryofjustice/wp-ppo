<?php

/**
 * Custom functions
 */
require_once locate_template( '/lib/extend-menus.php' );
require_once locate_template( '/lib/nav-mob.php' );

// Load CPTs
$cpt_declarations = scandir( get_template_directory() . "/lib/cpt/" );
foreach ( $cpt_declarations as $cpt_declaration ) {
	if ( $cpt_declaration[0] != "." )
		require get_template_directory() . '/lib/cpt/' . $cpt_declaration;
}

// Add image sizes
add_image_size( 'admin-list-thumb', 100, 100, false );
add_image_size( 'home-news-thumb', 180, 90, true );

// Add JS
function custom_scripts() {
	wp_enqueue_style( 'mlpush-component', get_template_directory_uri() . '/assets/css/component.css', false );
	wp_enqueue_style( 'mlpush-icons', get_template_directory_uri() . '/assets/css/icons.css', false );

	wp_register_script( 'equalheight', get_template_directory_uri() . '/assets/js/equalheight.js', array( 'jquery' ), null, false );
	wp_enqueue_script( 'equalheight' );
	wp_register_script( 'jquery.query-object', get_template_directory_uri() . '/assets/js/plugins/jquery.query-object.js', array( 'jquery' ), null, false );
	wp_enqueue_script( 'jquery.query-object' );
//	wp_register_script( 'isotope', get_template_directory_uri() . '/assets/js/vendor/isotope.min.js', array( 'jquery', 'jquery-masonry' ), null, false );
//	wp_enqueue_script( 'isotope' );
	wp_register_script( 'classie', get_template_directory_uri() . '/assets/js/classie.js', array(), null, false );
	wp_enqueue_script( 'classie' );
	wp_register_script( 'mlpushmenu', get_template_directory_uri() . '/assets/js/mlpushmenu.js', array(), null, false );
	wp_enqueue_script( 'mlpushmenu' );
	wp_register_script( 'modernizr.custom', get_template_directory_uri() . '/assets/js/modernizr.custom.js', array( 'jquery' ), null, false );
	wp_enqueue_script( 'modernizr.custom' );
}

add_action( 'wp_enqueue_scripts', 'custom_scripts', 100 );

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
add_filter( 'ot_use_theme_options', '__return_true' );

add_filter( 'ot_header_version_text', '__return_null' );

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
	if ( isset( $result ) ) {
		return $result;
	} else {
		return false;
	}
}

/* Returns file size */

function get_filesize( $file_url, $brackets = false ) {
	$attachment_id = get_attachment_id_from_src( $file_url );
	$local_path = get_attached_file( $attachment_id );
	return ($brackets ? "(" : "") . file_size_convert( filesize( $local_path ) ) . ($brackets ? ")" : "");
//	return $file_url;
}

/* Adds query params to menu items */
/*

  add_filter( 'wp_get_nav_menu_items','nav_items', 11, 3 );

  function nav_items( $items, $menu, $args )
  {
  if( is_admin() )
  return $items;

  foreach( $items as $item )
  {
  if( 'Home' == $item->post_title)
  $item->url .= '?my_var=test';

  }
  return $items;
  }

 */

/* Add custom filter to Documents listing */

function ppo_add_doc_filters() {
	global $typenow;

	// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
	$taxonomies = array( 'document_type' );

	// must set this to the post type you want the filter(s) displayed on
	if ( $typenow == 'document' ) {

		foreach ( $taxonomies as $tax_slug ) {
			$tax_obj = get_taxonomy( $tax_slug );
			$tax_name = $tax_obj->labels->name;
			$terms = get_terms( $tax_slug );
			$current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
			if ( count( $terms ) > 0 ) {
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>Show All $tax_name</option>";
				foreach ( $terms as $term ) {

					echo '<option value=' . $term->slug, $current_tax_slug == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
				}
				echo "</select>";
			}
		}
	}
}

add_action( 'restrict_manage_posts', 'ppo_add_doc_filters' );

// Sets document_type taxonomies to equal drop down value on save
$meta_keys = array( 'document-type', 'fii-death-type', 'fii-status' );

function update_document_type( $meta_id, $object_id, $meta_key, $meta_value ) {
	global $meta_keys;
	foreach ( $meta_keys as $current_meta_key ) {
		if ( $meta_key == $current_meta_key ) {
			wp_set_object_terms( $object_id, intval( $meta_value ), $current_meta_key );
		}
	}
}

add_action( 'update_post_meta', 'update_document_type', 10, 4 );

function add_document_type( $object_id, $meta_key, $meta_value ) {
	global $meta_keys;
	foreach ( $meta_keys as $current_meta_key ) {
		if ( $meta_key == $current_meta_key ) {
			wp_set_object_terms( $object_id, intval( $meta_value ), $current_meta_key );
		}
	}
}

add_action( 'add_post_meta', 'add_document_type', 10, 3 );

// add editor the privilege to edit theme
$roleObject = get_role( 'editor' );
if ( !$roleObject->has_cap( 'edit_theme_options' ) ) {
	$roleObject->add_cap( 'edit_theme_options' );
}

// Removes sidebar from entire site
add_filter( 'roots_display_sidebar', function() {
	return false;
} );

// Store current menu item ID in global var
function store_current_menu_id( $sorted_menu_items ) {
	foreach ( $sorted_menu_items as $menu_item ) {
		if ( $menu_item->current ) {
			$GLOBALS['current_menu_id'] = $menu_item->ID;
			break;
		}
	}
	return $sorted_menu_items;
}

add_filter( 'wp_nav_menu_objects', 'store_current_menu_id', 10, 2 );

// PPO breadcrumbs
function ppo_breadcrumbs() {
	if ( isset( $GLOBALS['current_menu_id'] ) ) {
		// Get main menu object
		$locations = get_nav_menu_locations();
		$menu = wp_get_nav_menu_object( $locations['primary_navigation'] );

		// Seperator between levels
		$seperator = ">";

		// Level 1 - Home level
		$level1_label = "Home";
		$level1_url = get_site_url();

		// Get remaining levels
		$level4_item = wp_get_nav_menu_items( $menu->term_id, array( 'p' => $GLOBALS['current_menu_id'] ) );
		$level4_label = $level4_item[0]->title;

		$level3_item = wp_get_nav_menu_items( $menu->term_id, array( 'p' => $level4_item[0]->menu_item_parent ) );
		$level3_label = $level3_item[0]->title;

		$level2_item = wp_get_nav_menu_items( $menu->term_id, array( 'p' => $level3_item[0]->menu_item_parent ) );
		$level2_label = $level2_item[0]->title;

		// Output breadcrumb
		$output = "<div id='breadcrumbs'>";
		$output .= "<a href='$level1_url'>$level1_label</a>";
		if ($level2_label!="Home") {
		$output .= " $seperator ";
		$output .= $level2_label;
		}
		if ($level3_label!="Home") {
		$output .= " $seperator ";
		$output .= $level3_label;
		}
		$output .= " <span class='current'>$seperator</span> ";
		$output .= $level4_label;
		$output .= "</div>";

		echo $output;
	}
}

// OptionTree filter to allow for textarea in list-item
add_filter( 'ot_override_forced_textarea_simple', '__return_true' );
