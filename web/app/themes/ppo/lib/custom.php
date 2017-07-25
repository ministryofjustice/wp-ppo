<?php

function case_types_save( $post_id, $post, $update ) {
	global $pagenow;
	$document_type = get_post_meta($post_id, 'document-type', true);

	if($document_type == 8) {
		if($pagenow == 'admin-ajax.php') {

			$casetypes = get_the_terms($post_id,'case-type');
			$cases = array();
			if(!empty($casetypes)) {
				foreach($casetypes as $casetype) {
					$cases[$casetype->term_id] = "$casetype->term_id";
				}
			}
			update_post_meta( $post_id, 'case-type', $cases);
		}

		if($pagenow == 'post.php') {
			$casetypes = get_post_meta( $post_id, 'case-type');
			$cases = array();
			if(!empty($casetypes[0])) {
				foreach ($casetypes[0] as $key => $value) {
					$cases[] = (int) $value;
				}
			}
			wp_set_post_terms( $post_id, $cases, 'case-type');
		}
	}
}
add_action( 'save_post', 'case_types_save', 10, 3 );


function create_news_post( $post_id, $post, $update ) {
	if(  wp_is_post_revision( $post_id) && wp_is_post_autosave( $post_id ) )
		return;

	$content = get_post_meta($post_id, 'document-description');
	if($content[0] != $_POST['document-description']) {
		$contentValue = $_POST['document-description'];
	} else {
		$contentValue = $content[0];
	}

	$file = get_post_meta( $post_id, 'document-upload' );
	if($file[0] != $_POST['document-upload']) {
		$fileValue = $_POST['document-upload'];
	} else {
		$fileValue = $file[0];
	}

	if($fileValue) {
		$contentValue .= '<p>Click here to read <a href="' . $fileValue . '">' . $post->post_title . '</p>';
	}

	if($post->post_type == "document" && isset( $_POST['create-news-item'])) {
		$newsitem = get_post_meta($post_id, 'news_item' );
		if(empty($newsitem) || is_string( get_post_status( $newsitem ) ) || get_post_status( $newsitem ) != "trash") {
			$post = array(
				'post_content' => $contentValue,
				'post_name' => $post->post_name,
				'post_title' => $post->post_title,
				'post_status' => 'draft',
				'post_type' => 'post',
				'post_date' => $post->post_date,
				'post_category' => array(35),
			);
			$value = wp_insert_post($post);
			if($value != "0") {
				update_post_meta( $post_id, 'news_item', $value );
			}
		}
	}
}
add_action( 'post_updated', 'create_news_post', 10, 3 );

function newsitem_add_meta_box() {
	add_meta_box(
		'newsitem',
		'Related News Item',
		'newsitem_callback',
		'document',
		'side'
	);
}
add_action( 'add_meta_boxes', 'newsitem_add_meta_box' );

function newsitem_callback( $post ) {
	$value = get_post_meta( $post->ID, 'news_item', true );
	if($value && is_string( get_post_status( $value ) ) && get_post_status( $value ) != "trash") {
		echo '<a href="' . admin_url() . 'post.php?post=' . $value . '&action=edit">Edit news items</a>';
	} else {
		echo "Do you want a related news item? It will replicate a news item with the same title and content.";
		echo '<br /><br /><input type="checkbox" name="create-news-item" value="create-news-item">';
	}
}



function shorturl_add_meta_box() {
	add_meta_box(
		'shorturl',
		'Short URL to Document',
		'shorturl_callback',
		'document',
		'side'
	);
}
add_action( 'add_meta_boxes', 'shorturl_add_meta_box' );

function shorturl_callback( $post ) {
	$value = get_post_meta( $post->ID, 'document-upload', true );
	if($value) {
		$postid = get_attachment_id_from_src( $value );
		echo wp_get_shortlink($postid);
	} else {
		echo "Please upload a document and save the page to see the Short URL.";
	}
}

function my_page_template_redirect()
{
    if( is_attachment() )
    {
        wp_redirect( wp_get_attachment_url() );
        exit();
    }
}
add_action( 'template_redirect', 'my_page_template_redirect' );

/**
 * [remove_document_meta description]
 * Removes document meta boxes for document type
 * @return void
 */
function remove_document_meta() {
	remove_meta_box( 'fii-death-typediv','document', 'side' );
	remove_meta_box( 'document_typediv','document', 'side' );
	remove_meta_box( 'fii-statusdiv','document', 'side' );
	remove_meta_box( 'case-typediv','document', 'side' );

}
add_action( 'admin_menu' , 'remove_document_meta' );

function my_acf_admin_head()
{
	?>
	<script type="text/javascript">
	(function($){
		if($('#document-type').val() != 34) {
		  $('#document-fii-meta-box').hide();
		}
		if($('#document-type').val() != 8) {
		  $('#document-llr-meta-box').hide();
		 }
		$( "#document-type" ).change(function() {
		  if($('#document-type').val() == 34) {
		  	$('#document-fii-meta-box').show();
		  	$('#document-llr-meta-box').hide();
		  } else if($('#document-type').val() == 8) {
		  	$('#document-llr-meta-box').show();
		  	$('#document-fii-meta-box').hide();
		  } else {
		  	$('#document-fii-meta-box').hide();
		  	$('#document-llr-meta-box').hide();
		  }
		});
	})(jQuery);
	</script>
	<style type="text/css">
		#setting_document-type .select-wrapper { width:99%; }
	</style>

	<?php
}
add_action('admin_footer', 'my_acf_admin_head');

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
add_image_size( 'home-news-thumb', 158, 224, false );

// Add JS
function custom_scripts() {
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
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_use_theme_options', '__return_true' );
add_filter( 'ot_header_version_text', '__return_null' );

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
			wp_set_object_terms( $object_id, intval( $meta_value ), $current_meta_key != 'fii-death-type' ? str_replace( "-", "_", $current_meta_key ) : $current_meta_key  );
		}
	}
}

add_action( 'update_post_meta', 'update_document_type', 10, 4 );

function add_document_type( $object_id, $meta_key, $meta_value ) {
	global $meta_keys;
	foreach ( $meta_keys as $current_meta_key ) {
		if ( $meta_key == $current_meta_key ) {
			wp_set_object_terms( $object_id, intval( $meta_value ), $current_meta_key != 'fii-death-type' ? str_replace( "-", "_", $current_meta_key ) : $current_meta_key  );
		}
	}
}

add_action( 'add_post_meta', 'add_document_type', 10, 3 );

function fix_datepicker_format( $post_id ) {
	// Standardise date format to dd/mm/yyyy
	foreach ( array( 'document-date', 'fii-death-date' ) as $index ) {
		if ( isset( $_REQUEST[$index] ) ) {
			$date_parts = explode( "/", $_REQUEST[$index] );
			$day = sprintf( "%02d", $date_parts[0] );
			$month = sprintf( "%02d", $date_parts[1] );
			if ( strlen( $date_parts[2] ) == 2 ) {
				$year = $date_parts[2] > 50 ? "19" . $date_parts[2] : "20" . $date_parts[2];
			} else {
				$year = $date_parts[2];
			}
			$new_date = $day . "/" . $month . "/" . $year;
			update_post_meta( $post_id, $index, $new_date );
		}
	}
}

add_action( 'save_post', 'fix_datepicker_format' );

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
		$level1_url = get_home_url();

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
		if ( $level2_label != "Home" ) {
			$output .= " $seperator ";
			$output .= $level2_label;
		}
		if ( $level3_label != "Home" ) {
			$output .= " $seperator ";
			$output .= $level3_label;
		}
		$output .= " $seperator <span class='current'>";
		$output .= $level4_label;
		$output .= "</span></div>";

		echo $output;
	}
}

// OptionTree filter to allow for textarea in list-item
add_filter( 'ot_override_forced_textarea_simple', '__return_true' );

// Force is_search to be set
add_action( 'parse_query', 'search_even_empty' );

function search_even_empty( $query ) {
	if ( isset( $_GET['s'] ) ):
		$query->is_search = true;
	endif;
}

// Removes post types added by Custom Search plugin
remove_filter( 'pre_get_posts', 'cstmsrch_searchfilter' );

// Temporary filter for convering dates to sort by
function wdw_query_orderby_postmeta_date( $orderby ) {
	$new_orderby = str_replace( "wp_postmeta.meta_value", "STR_TO_DATE(wp_postmeta.meta_value, '%d/%m/%Y')", $orderby );
	return $new_orderby;
}

// Add pages to search
function wpshock_search_filter( $query ) {
	$docs_only = (isset( $query->query['docs_only'] ) && $query->query['docs_only']) ? true : false;
	if ( $query->is_search && !is_admin() && !$docs_only ) {
		$query->set( 'post_type', array( 'post', 'page' ) );
	}
	return $query;
}

add_filter( 'pre_get_posts', 'wpshock_search_filter' );

