<?php $home_id = get_the_ID(); ?>
<div id="home-cta-container" class="container">
	<div class="row">
		<?php for ( $i = 1; $i <= 4; $i++ ) { ?>
			<div class="col-md-3 home-cta">
				<a href="<?php echo ot_get_option( "homepage_nav_url$i" ); ?>">
					<div class="cta-inner">
						<h2><?php echo ot_get_option( "homepage_nav_title$i" ); ?></h2>
						<div><?php echo ot_get_option( "homepage_nav_text$i" ); ?></div>
						<img src="<?php echo ot_get_option( "homepage_nav_image$i" ); ?>">
					</div>
				</a>
			</div>
		<?php } ?>
	</div>
</div>

<div id="home-content-container" class="container">
	<div class="row">
		<div class="col-md-6">
			<h1>Welcome to the PPO</h1>

			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>

			<?php get_search_form( true ); ?>
			<div id="latest-publications" class="boxout">
				<h3>Latest Publications</h3>
				<ul>
					<?php
					// Get meta value containing array of entries
					$latest_publications_args = array(
						'post_type' => 'document',
						'posts_per_page' => 3
					);
					$latest_publications_query = new WP_Query( $latest_publications_args );
					// Iterate over entries and display
					while ( $latest_publications_query->have_posts() ) : $latest_publications_query->the_post();
						?>
						<li><i class="fa fa-file-o fa-lg"></i>
							<a href="<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>">
								<?php
								$document_date = get_metadata( 'post', get_the_ID(), 'document-date', true );
								$document_datetime = date( "Y", strtotime( str_replace( "/", "-", $document_date ) ) );
								echo $document_datetime;
								?>,
								<?php echo get_the_title( get_metadata( 'post', get_the_ID(), 'fii-establishment', true ) ); ?> 
								(<?php $death_type_array = get_term( get_metadata( 'post', get_the_ID(), 'fii-death-type', true ), 'fii-death-type', 'ARRAY_N' ); echo $death_type_array[1]; ?>)
							</a>
						</li>
						<?php
					endwhile;
					?>
				</ul>
			</div>
		</div>
		<div class="col-md-6">

			<div class='row'>
				<div id="latest-news" class="boxout">
					<h3>Latest news</h3>
					<ul>
						<?php
						// Get meta value containing array of entries
						$latest_news_args = array(
							'post_type' => 'post',
							'posts_per_page' => 4
						);
						$latest_news_query = new WP_Query( $latest_news_args );
						// Iterate over entries and display
						while ( $latest_news_query->have_posts() ) : $latest_news_query->the_post();
							?>
							<li>
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'home-news-thumb' ); ?></a>
								<div class="news-details">
									<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
									<?php the_excerpt(); ?>
								</div>
							</li>
							<?php
						endwhile;
						?>
					</ul>
				</div>
				<div id='ppo-info' class='col-sm-6'>
					<h2>PPO Information</h2>
					<ul>
						<?php
						// Get meta value containing array of entries
						$new_entries_array = get_post_meta( $home_id, 'ppo-info-content' );
						$new_entries = $new_entries_array[0];
						// Iterate over entries and display
						foreach ( $new_entries as $entry ) {
							?>		
							<li>
								<h5><a href="<?php echo get_permalink( $entry['link'] ); ?>"><?php echo $entry['title']; ?></a></h5>
								<?php echo $entry['content']; ?>
							</li>
							<?php
						}
						?>
					</ul>
				</div>

				<div id='quick-links' class='col-sm-6'>
					<h2>Quick Links</h2>
					<ul>
						<?php
						// Get meta value containing array of entries
						$new_entries_array = get_post_meta( $home_id, 'quick-links-content' );
						$new_entries = $new_entries_array[0];
						// Iterate over entries and display
						foreach ( $new_entries as $entry ) {
							?>		
							<li>
								<h5><a href="<?php echo get_permalink( $entry['link'] ); ?>"><?php echo $entry['title']; ?></a></h5>
								<?php echo $entry['content']; ?>
							</li>
							<?php
						}
						?>
					</ul>
				</div>

			</div>
		</div>
	</div>
</div>