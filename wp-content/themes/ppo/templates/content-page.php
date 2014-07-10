<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
	<?php wp_link_pages( array( 'before' => '<nav class="pagination">', 'after' => '</nav>' ) ); ?>

	<?php
	// Set up the objects needed
	$my_wp_query = new WP_Query();
	$all_wp_pages = $my_wp_query->query( array( 'post_type' => 'page', 'posts_per_page' => -1, 'order' => 'ASC', 'order_by' => 'position' ) );

	// Filter through all pages and find Portfolio's children
	// $page_children = get_pages('child_of=' . get_the_ID() .'&hierarchical=0&parent=' . get_the_ID() . '&sort_column=menu_order');
	// Filter through all pages and find Portfolio's siblings
	$page_parents = get_post_ancestors( get_the_ID() );
	$page_siblings = get_pages( 'child_of=' . $page_parents[0] . '&hierarchical=0&sort_column=menu_order' );

//	if ( count( $page_siblings ) && false) {
//		echo "<div class='child-pages'>";
//		foreach ( $page_siblings as $child ) {
//			
	?>
	<!--			<li>
					<a href="//<?php echo get_permalink( $child->ID ); ?>"><?php echo $child->post_title; ?></a>
				</li>		-->
	<?php
//		}
//		echo "</div>";
//	}

	$section_id = empty( $post->ancestors ) ? $post->ID : end( $post->ancestors );
	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations['primary_navigation'] );
	$cur_menu_item = wp_get_nav_menu_items( $menu->term_id, array( 'post_parent' => $section_id ) );
	$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
	if ( !empty( $menu_items ) ) {
		echo '<ul class="section-submenu">';
		foreach ( $menu_items as $menu_item ) {
			if ( $menu_item->menu_item_parent == $cur_menu_item[0]->ID ) {
				echo '<li><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
			}
		}
		echo '</ul>';
	}
	?>

<?php endwhile; ?>

<pre>

	<?php
//	var_dump( $menu_items );
	?>

</pre>
