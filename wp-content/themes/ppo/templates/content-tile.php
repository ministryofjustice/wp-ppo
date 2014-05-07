<?php
$doc_type_array = get_the_terms( get_the_ID(), 'document_type' );
$doc_types = array();
foreach ( $doc_type_array as $doc_type ) {
	$doc_types[] = str_replace(" ","-",strtolower($doc_type->name));
}

$doc_classes = join( " ", $doc_types );
?>

<article id="<?php echo 'doc-' . get_the_ID(); ?>" class="<?php echo $doc_classes; ?>">
	<a href="<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ) ?>">
		<div class="tile-image">
			<?php the_post_thumbnail(); ?>
		</div>
		<div class="tile-details">
			<h3><?php the_title(); ?></h3>
			<h4><?php echo get_metadata( 'post', get_the_ID(), 'document-date', true ) ?></h4>
			<div class="tile-size">
				<?php echo (get_filesize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ) )); ?>
			</div>
		</div>
	</a>
</article>