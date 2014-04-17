<h1>Welcome to the PPO</h1>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; ?>

<?php get_search_form(true); ?>

<div id="whats-new">
	<h2>What's new</h2>
	<ul>
		<?php
		// Get meta value containing array of entries
		$new_entries_array = get_post_meta( get_the_ID(), 'whats-new-content' );
		$new_entries = $new_entries_array[0];
		// Split entries into date and content columns
		foreach ( $new_entries as $key => $row ) {
			$date[$key] = $new_entries[$key]['date'] = strtotime( $row['date'] );
			$content[$key] = $row['content'];
		}
		// Sort data as per the home-order option
		switch ( get_post_meta( get_the_ID(), 'home-order' )[0] ) {
			case 'abc': // Chronological
				array_multisort( $date, SORT_ASC, $content, SORT_ASC, $new_entries );
				break;
			case 'cba': // Reverse chronological
				array_multisort( $date, SORT_DESC, $content, SORT_ASC, $new_entries );
				break;
			default: // Default (manual)
		}
		// Iterate over entries and display
		foreach ( $new_entries as $entry ) {
			?>		
			<li>
				<?php echo date( "j F Y", $entry['date'] ); ?> - 
				<?php echo $entry['content']; ?>
			</li>
			<?php
		}
		?>
	</ul>
</div>

<div class='row'>

	<div id='ppo-info' class='col-sm-6'>
		<h2>PPO Information</h2>
		<ul>
			<?php
			// Get meta value containing array of entries
			$new_entries = get_post_meta( get_the_ID(), 'ppo-info-content' )[0];
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
			$new_entries = get_post_meta( get_the_ID(), 'quick-links-content' )[0];
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