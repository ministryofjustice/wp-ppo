<?php
$doc_type_array = get_the_terms( get_the_ID(), 'document_type' );
$doc_types = array();
foreach ( $doc_type_array as $doc_type ) {
	$doc_types[] = str_replace( " ", "-", strtolower( $doc_type->name ) );
}

$doc_classes = join( " ", $doc_types );

$document_date = get_metadata( 'post', get_the_ID(), 'document-date', true );
$document_datetime = date( "Y-m-d", strtotime( str_replace( "/", "-", $document_date ) ) );
$document_decade = substr($document_datetime, 0,3) . "0";

$document_size = get_filesize( get_metadata( 'post', get_the_ID(), 'document-upload', true ) );
?>

<article id="<?php echo 'doc-' . get_the_ID(); ?>" class="<?php echo $doc_classes; ?>" data-doc-date="<?php echo $document_datetime; ?>" data-size="<?php echo $document_size; ?>" data-decade="<?php echo $document_decade; ?>">
	<div class="tile-image">
		<a href="<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>">
			<?php the_post_thumbnail(); ?>
		</a>
	</div>
	<div class="tile-details">
		<h3><?php the_title(); ?></h3>
		<h4><?php echo $document_date; ?></h4>
		<div class="tile-size">
			<?php echo $document_size; ?>
		</div>
		<div class="tile-social">
			<a href="//twitter.com/intent/tweet?url=<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>&text=<?php echo get_the_title(); ?>&via=sparkdevelop" target="_blank">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/twitter.png" alt="Share on Twitter" title="Share on Twitter">
			</a>
		</div>
	</div>
</article>