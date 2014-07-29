<?php get_template_part( 'templates/page', 'header' ); ?>

<div class="row">



	<div class="col-lg-8 col-sm-12 tile-container">
		<h2>Documents</h2>
		<?php
		// Create search query to search for documents and pages
		$args = array(
			'post_type' => 'document',
			'posts_per_page' => -1,
			's' => get_search_query()
		);
		$matching_docs = new WP_Query( $args );
		?>
		<?php if ( !$matching_docs->have_posts() ) : ?>
			<div class="alert alert-warning">
				<?php _e( 'Sorry, no results were found.', 'roots' ); ?>
			</div>
			<?php get_search_form(); ?>
		<?php endif; ?>
		<?php
		while ( $matching_docs->have_posts() ) : $matching_docs->the_post();
			?>
			<?php get_template_part( 'templates/content-tile', get_post_format() ); ?>
		<?php endwhile; ?>

	</div>

	<div class="col-lg-4 col-sm-12">
		<h2>Pages</h2>
		<?php
		// Modify search query to search for documents and pages
		$args = array(
			'post_type' => 'post'
		);
		query_posts( $args );
		while ( have_posts() ) : the_post();
			?>
			<?php get_template_part( 'templates/content-tile', get_post_format() ); ?>
		<?php endwhile; ?>

		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav class="post-nav">
				<ul class="pager">
					<li class="previous"><?php next_posts_link( __( '&larr; Older posts', 'roots' ) ); ?></li>
					<li class="next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'roots' ) ); ?></li>
				</ul>
			</nav>
		<?php endif; ?>

	</div>

</div>