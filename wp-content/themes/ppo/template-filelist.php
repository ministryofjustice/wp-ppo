<?php
/*
  Template Name: Filelist Page
 */
get_template_part( 'templates/page', 'header' );
?>

<div class = "entry-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</div>

<?php
$files = get_post_meta( get_the_ID(), 'filelist-entries' );
$file_count = count( $files[0] );
?>

<section class="filelist">
	<?php echo "<header>" . $file_count . " " . ngettext( "file", "files", $file_count ) . " found</header>"; ?>
	<ul>
		<?php
		if ( $files[0] ) {
			foreach ( $files[0] as $file ) {
				echo "<li><a href='" . $file['file'] . "'>" . $file['title'] . "</a> " . get_filesize( $file['file'], true ) . "</li>";
				echo $file['date'];
			}
		}
		?>
	</ul>
</section>