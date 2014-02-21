<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
	<?php wp_link_pages( array( 'before' => '<nav class="pagination">', 'after' => '</nav>' ) ); ?>

	<?php
	// Set up the objects needed
	$my_wp_query = new WP_Query();
	$all_wp_pages = $my_wp_query->query( array( 'post_type' => 'page', 'posts_per_page' => -1, 'order' => 'ASC', 'order_by' => 'position' ) );

	// Filter through all pages and find Portfolio's children
	$page_children = get_page_children( get_the_ID(), $all_wp_pages );

	if ( count( $page_children ) ) {
		echo "<div class='child-pages'>";
		foreach ( $page_children as $child ) {
			?>
			<li>
				<a href="<?php echo get_permalink( $child->ID ); ?>"><?php echo $child->post_title; ?></a>
			</li>		
			<?php
		}
		echo "</div>";
	}
	?>

<?php endwhile; ?>
