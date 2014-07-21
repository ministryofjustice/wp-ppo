<?php 
// Set classes for tile based in document type (there should only be one)
$doc_type_array = get_the_terms( get_the_ID(), 'document_type' );
$doc_types = array();
foreach ( $doc_type_array as $doc_type ) {
	$doc_types[] = str_replace( " ", "-", strtolower( $doc_type->name ) );
}
$doc_classes = join( " ", $doc_types );

$document_date = get_metadata( 'post', get_the_ID(), 'document-date', true );
$document_datetime = date( "Y-m-d", strtotime( str_replace( "/", "-", $document_date ) ) );
$document_decade = substr( $document_datetime, 0, 3 ) . "0";

$death_date = get_metadata( 'post', get_the_ID(), 'fii-death-date', true );

$document_size = get_filesize( get_metadata( 'post', get_the_ID(), 'document-upload', true ) );

// Set flags for determining document type
$fii_term_object = get_term_by('slug','fii-report','document-type');
$is_fii = ($doc_types[0] == "fii-report" || (get_metadata( 'post', get_the_ID(), 'document-type' ) == $fii_term_object['term_id']) ? true : false);

// Conditionals for document type
if ( $is_fii ) {
	$document_establishment_id = get_metadata( 'post', get_the_ID(), 'fii-establishment', true );
	$document_establishment_type = get_metadata( 'post', $document_establishment_id, 'establishment-type', true );

	$document_death_types = wp_get_post_terms( get_the_ID(), 'fii-death-type', array( "fields" => "ids" ) );
	if ( isset( $document_death_types[0] ) ) {
		$document_death_type = $document_death_types[0];
	} else {
		$document_death_type = "";
	}
}

// Construct the tile metadata
$tile_data = " data-date='" . $document_datetime . "'"
		. " data-size='" . $document_size . "'"
		. " data-decade='" . $document_decade . "'"
		. ($is_fii ? " data-fii-death-type='" . str_replace( " ", "-", strtolower( $document_death_type ) ) . "'" : "")
		. ($is_fii ? " data-establishment-type='" . $document_establishment_type . "'" : "");
?>

<article id="<?php echo 'doc-' . get_the_ID(); ?>" class="<?php echo $doc_classes; ?>"<?php echo $tile_data; ?>>
	<a href="<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>">
		<?php if ( !$is_fii ) { ?>
			<div class="tile-image">
				<?php the_post_thumbnail(); ?>
			</div>
		<?php } ?>
		<?php if ( !$is_fii ) { ?>
			<div class="tile-details">
				<h3><?php the_title(); ?></h3>
				<h4>Published: <?php echo $document_date; ?></h4>
			</div>
		<?php } else { ?>
			<h3><?php echo get_the_title( $document_establishment_id ) ? get_the_title( $document_establishment_id ) : "&nbsp;"; ?></h3>
			<div class="tile-details">
				<table>
					<tr>
						<td><strong>
							<?php
							if ( $document_establishment_type ) {
								echo get_term_field( "name", $document_establishment_type, 'establishment-type' );
							}
							?>
							</strong></td>
					</tr>
					<tr>
						<td>Published:</td> 
						<td><?php echo $document_date; ?></td>
					</tr>
					<tr>
						<td>Date of death:</td>
						<td><?php echo $death_date; ?></td>
					</tr>
					<tr>
						<td>Type of death:</td>
						<td><?php echo $document_death_type ? get_term_field( "name", $document_death_type, 'fii-death-type' ) : ""; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo (get_metadata( 'post', get_the_ID(), 'fii-gender', true ) == "m" ? "Male" : "Female"); ?></td>
						<td><?php echo get_metadata( 'post', get_the_ID(), 'fii-age', true ); ?></td>
					</tr>
				</table>
			</div>
		<?php } ?>
		<!--		<div class="tile-size">
		<?php echo $document_size; ?>
				</div>-->
		<!--		<div class="tile-social">
					<a href="//twitter.com/intent/tweet?url=<?php echo get_metadata( 'post', get_the_ID(), 'document-upload', true ); ?>&text=<?php echo get_the_title(); ?>&via=sparkdevelop" target="_blank">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/icons/twitter.png" alt="Share on Twitter" title="Share on Twitter">
					</a>
				</div>-->
		</div>
	</a>
</article>