<?php

$pdf_url = get_post_meta( get_the_ID(), 'document-upload', true );
if ( $pdf_url ) {
	wp_redirect( $pdf_url, '301' );
}

?>

<?php get_template_part( 'templates/content', 'single' ); ?>

<div class="navigation">
	<div class="textleft col-md-6">
		<?php previous_post_link("<span class='glyphicon glyphicon-chevron-left'></span> %link"); ?>
	</div>
	<div class="textright col-md-6">
		<?php next_post_link("%link <span class='glyphicon glyphicon-chevron-right'></span>"); ?>
	</div>
</div> <!-- end navigation -->