<?php
/* 
Template Name: Category news archive
*/
?>


<div class="page-header">
	<?php the_title( '<h1>', '</h1>' ); ?>
</div>



<div class="news-archive-links">
	<ul>


		<?php 
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			query_posts('cat=news&posts_per_page=10&paged=' . $paged); ?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


		  <li>
		  <a href="<?php the_permalink() ?>"><?php the_post_thumbnail( 'home-news-thumb' ); ?></a>
		  <h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
		  <time class="published"><?php echo get_the_date(); ?></time>

		    <?php the_excerpt() ?>
		    
		  </li>
		  <hr/>
	    <?php endwhile; endif; ?>


	</ul>

<div class="pagination">
	<?php echo paginate_links( $args ); ?>
</div>

<!-- <div class="nav-previous alignleft"><?php next_posts_link( 'Older posts' ); ?></div>
<div class="nav-next alignright"><?php previous_posts_link( 'Newer posts' ); ?></div>  -->

</div>