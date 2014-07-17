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

	// Converts dates to datetime for correct ordering
	add_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1 );

	$args_json = $_POST['queryParams'];

	// Decode JSON to array
	$args = json_decode( stripslashes( $args_json ), true );

	foreach ( $args[tax_query] as $i => $tax_query ) {
		if ( $tax_query[taxonomy] = "establishment-type" ) {
			// Remove taxonomy query
			unset($args[tax_query][$i]);

			// Retrieve matching establishment IDs
			$matching_establishments_args = array(
				'post_type' => 'establishment',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'establishment-type',
						'terms' => $tax_query[terms]
					)
				)
			);

			$matching_establishments = new WP_Query( $matching_establishments_args );

			while ( $matching_establishments->have_posts() ) {
				$matching_establishments->the_post();
				$matching_establishments_ids[] = get_the_ID();
			}

			// Add custom field query
			$args[meta_query][] = array(
				'key' => 'fii-establishment',
				'value' => $matching_establishments_ids,
				'compare' => 'IN',
				'include_children' => false
			);

			wp_reset_postdata();
		}
	}

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
	remove_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1 );
	die();
}

add_action( 'wp_ajax_nopriv_update_tiles', 'update_tiles' );
add_action( 'wp_ajax_update_tiles', 'update_tiles' );
