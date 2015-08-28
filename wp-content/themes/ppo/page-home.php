<?php include(dirname( __FILE__ ) . '/lib/ppo-debug.php'); // Add debug tools ?>

<div id="home-content-container" class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>Welcome to the PPO</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
		<div class="col-md-6">
			<?php get_search_form( true ); ?>
			<iframe width="1280" height="720" title="Youtube video" src="//www.youtube.com/embed/Da7tH1LLRVE?modestbranding=1&showinfo=0&autohide=1&rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>

	<?php $home_id = get_the_ID(); ?>
	<div id="home-cta-container" class="container">
		<div class="row">
			<?php for ( $i = 1; $i <= 4; $i++ ) { ?>
				<div class="col-xs-12 col-sm-6 col-md-3 home-cta">
					<a href="<?php echo ot_get_option( "homepage_nav_url$i" ); ?>">
						<div class="cta-inner">
							<h2><?php echo ot_get_option( "homepage_nav_title$i" ); ?></h2>
							<div><?php echo ot_get_option( "homepage_nav_text$i" ); ?></div>
							<img src="<?php echo ot_get_option( "homepage_nav_image$i" ); ?>" alt="<?php echo ot_get_option( "homepage_nav_image_alt$i" ); ?>">
						</div>
					</a>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="col-md-6">
		<div id="latest-publications" class="boxout">
			<h3>Latest Publications</h3>
			<ul>
				<?php
				// Converts dates to datetime for correct ordering
				add_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1 );

				// Get meta value containing array of entries
				$latest_publications_args = array(
					'post_type' => 'document',
					'posts_per_page' => 5,
					'tax_query' => array(
						array(
							'taxonomy' => 'document_type',
							'field' => 'slug',
							'terms' => array( 'annual-reports', 'learning-lessons-reports', 'stakeholder-feedback' ),
							'operator' => 'IN'
						)
					),
					'orderby' => 'meta_value',
					'meta_key' => 'document-date'
				);
				$latest_publications_query = new WP_Query( $latest_publications_args );
				// Iterate over entries and display
				while ( $latest_publications_query->have_posts() ) : $latest_publications_query->the_post();
					?>
					<li>
						<a href="<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>">
							<?php
//							$document_date = get_metadata( 'post', get_the_ID(), 'document-date', true );
//							$document_datetime = date( "Y", strtotime( str_replace( "/", "-", $document_date ) ) );
//							echo $document_datetime;
							?>
							<?php echo get_the_title( get_metadata( 'post', get_the_ID(), 'fii-establishment', true ) ); ?>
							<?php
							$death_type_array = get_term( get_metadata( 'post', get_the_ID(), 'fii-death-type', true ), 'fii-death-type', 'ARRAY_N' );
							if ( !is_wp_error( $death_type_array ) ) {
								echo " ($death_type_array[1])";
							}
							?>
						</a>
					</li>
					<?php
				endwhile;

				remove_filter( 'posts_orderby', 'wdw_query_orderby_postmeta_date', 10, 1 );
				?>
			</ul>
			<?php
			$anon_reports = new WP_Query( array(
				'post_type' => 'document',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'document_type',
						'field' => 'slug',
						'terms' => 'fii-report'
					)
				),
				'date_query' => array(
					array(
						'after' => '1 week ago'
					)
				)
					) );
			?>
			<a href='<?php echo site_url( 'last-seven-days' ); ?>'>
				<div id="anon-count-container">
					<div id='anon-count-text'>
						Anonymised reports added in the last 7 days<br>(click to view)
					</div>
					<div id="anon-count">
						<?php echo $anon_reports->post_count; ?>
					</div>
				</div>
			</a>
		</div>
		<div id="newsletter-subscribe" class="boxout">
			<h3>Subscribe to our Newsletter</h3>
			<p>If you would like to receive email updates about new publications please sign up for our mailing list.</p>
			<a href="http://gsi.us8.list-manage.com/subscribe?u=af164ebad7153bb6568f0f296&id=65fcc6544b" target="_blank" class="btn btn-primary btn-lg btn-block">Subscribe</a>
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
						'posts_per_page' => 2
					);
					$latest_news_query = new WP_Query( $latest_news_args );
					// Iterate over entries and display
					while ( $latest_news_query->have_posts() ) : $latest_news_query->the_post();
						?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'home-news-thumb' ); ?></a>
							<div class="news-details">
								<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
								<time class="published" datetime="<?php echo get_the_time( 'c' ); ?>"><?php echo get_the_date(); ?></time>
									<?php the_excerpt(); ?>
							</div>
						</li>
						<?php
					endwhile;
					?>
				</ul>

				<a class="news-archive-link" href="news-archive/">View news archive</a>
			</div>
		</div>
	</div>
</div>
