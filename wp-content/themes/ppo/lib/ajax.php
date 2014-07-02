<?php

/*
 * Setup for front-end AJAX
 */

function wdw_query_orderby_postmeta_date( $orderby ) {
	$new_orderby = str_replace( "wp_postmeta.meta_value", "STR_TO_DATE(wp_postmeta.meta_value, '%d/%m/%Y')", $orderby );
	return $new_orderby;
}

function ajax_scripts() {
	wp_enqueue_script( 'ppo-ajax', get_template_directory_uri( __FILE__ ) . '/assets/js/ajax.js', array( 'jquery' ) );
	wp_localize_script( 'ppo-ajax', 'PPOAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'ajax_scripts', 101 );

function update_tiles() {
	add_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1);
	$args = $_POST['queryParams'];
	$ajax_query = new WP_Query( $args );
	ob_start();
?>
	<?php while ( $ajax_query->have_posts() ) : $ajax_query->the_post(); ?>
		<?php get_template_part( 'templates/content-tile', get_post_format() ); ?>
	<?php endwhile; ?>
	<?php

	$tile_output = ob_get_contents();
	ob_end_clean();
	echo $tile_output;
	remove_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1);
	die();
}

add_action( 'wp_ajax_nopriv_update_tiles', 'update_tiles' );
add_action( 'wp_ajax_update_tiles', 'update_tiles' );
