<?php

// For debugging only - performance slowdown when turned on

// Setup post types and associated taxonomies
$post_types = array();
$post_types[] = array( 'name' => 'document', 'taxonomies' => array( 'document_type' ) );
$post_types[] = array( 'name' => 'establishment', 'taxonomies' => array( 'establishment-type' ) );
foreach ( $post_types as $post_type ) {
	$cpt_name = $post_type['name'];
	if ( isset( $_GET['debug'.$cpt_name] ) && $_GET['debug'.$cpt_name] == 1 ) {
		$posts_array = new WP_Query( array(
			'post_type' => $cpt_name,
			'posts_per_page' => -1,
				) );
		$taxonomies = $post_type['taxonomies'];
		if ( isset( $_GET['fix'] ) && $_GET['fix'] == 1 ) {
			while ( $posts_array->have_posts() ) {
				$posts_array->the_post();
				foreach ( $taxonomies as $tax ) {
					$pt = wp_get_post_terms( get_the_ID(), $tax );
					if ( count( $pt ) == 1 && (get_post_meta( get_the_ID(), $tax, true ) != $pt[0]->term_id || get_post_meta( get_the_ID(), str_replace( "_", "-", $tax ), true ) != $pt[0]->term_id) ) {
						update_post_meta( get_the_ID(), $tax, $pt[0]->term_id );
						update_post_meta( get_the_ID(), str_replace( "_", "-", $tax ), $pt[0]->term_id ); // Having to compensate for inconsistencies in variable notation (_ and -)
						echo "<p>" . ucfirst($cpt_name) . "  " . get_the_ID() . " has " . str_replace( "_", " ", $tax ) . " set to " . $pt[0]->term_id . "</p>";
					}
				}
			}
		} else {
			while ( $posts_array->have_posts() ) {
				$posts_array->the_post();
				foreach ( $taxonomies as $tax ) {
					$pt = wp_get_post_terms( get_the_ID(), $tax );
					if ( count( $pt ) != 1 ) {
						echo "<p>Document " . get_the_ID() . " has " . count( $pt ) . " " . str_replace( "_", " ", $tax ) . "</p>";
					} elseif ( get_post_meta( get_the_ID(), $tax, true ) != $pt[0]->term_id && get_post_meta( get_the_ID(), str_replace( "_", "-", $tax ), true ) != $pt[0]->term_id ) {
						echo "<p>Document " . get_the_ID() . " has mismatched " . $tax . "s (" . get_post_meta( get_the_ID(), $tax, true ) . " & " . $pt[0]->term_id . ")</p>";
					}
				}
			}
		}
		die();
	}
}
/*	
} elseif ( isset( $_GET['debugest'] ) && $_GET['debugest'] == 1 ) {
	$est_array = new WP_Query( array(
		'post_type' => 'establishment',
		'posts_per_page' => -1,
			) );
	if ( isset( $_GET['fix'] ) && $_GET['fix'] == 1 ) {
		while ( $est_array->have_posts() ) {
			$est_array->the_post();
			$pt = wp_get_post_terms( get_the_ID(), 'establishment-type' );
			if ( count( $pt ) == 1 && get_post_meta( get_the_ID(), "establishment-type", true ) != $pt[0]->term_id ) {
				update_post_meta( get_the_ID(), "establishment-type", $pt[0]->term_id );
				echo "<p>Establishment " . get_the_ID() . " has establishment type set to " . $pt[0]->term_id . "</p>";
			}
		}
	} else {
		while ( $est_array->have_posts() ) {
			$est_array->the_post();
			$pt = wp_get_post_terms( get_the_ID(), 'establishment-type' );
			if ( count( $pt ) != 1 ) {
				echo "<p>Establishment " . get_the_ID() . " has " . count( $pt ) . " establishment types</p>";
			} elseif ( get_post_meta( get_the_ID(), "establishment-type", true ) != $pt[0]->term_id ) {
				echo "<p>Establishment " . get_the_ID() . " has mismatched establishment types (" . get_post_meta( get_the_ID(), "establishment-type", true ) . " & " . $pt[0]->term_id . ")</p>";
			}
		}
	}
	die();
}
 */