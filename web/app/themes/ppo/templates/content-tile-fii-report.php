<?php

$id = get_the_ID();
$document_type = get_the_terms($id, 'document_type');
$document_type = $document_type[0]->slug;

$document_date = get_post_meta($id, 'document-date', true);
$document_datetime = date( "Y-m-d", strtotime( str_replace( "/", "-", $document_date ) ) );
$document_decade = substr( $document_datetime, 0, 3 ) . "0";

$document_upload = get_post_meta($id, 'document-upload', true);
$document_size = get_filesize($document_upload);

// Fatal Incident report specific
$document_establishment_id = get_post_meta($id, 'fii-establishment', true);
$document_establishment_type = get_post_meta($document_establishment_id, 'establishment-type', true);

$document_death_types = wp_get_post_terms( $id, 'fii-death-type' );
if ( !is_wp_error($document_death_types) && count($document_death_types) > 0 ) {
  $document_death_type = $document_death_types[0];
} else {
  $document_death_type = false;
}

$death_date = get_post_meta($id, 'fii-death-date', true);

?>
<article id="<?= 'doc-' . $id ?>"
         class="<?= esc_attr($document_type) ?>"
         data-date="<?= esc_attr($document_datetime) ?>"
         data-size="<?= esc_attr(get_filesize($document_upload)) ?>"
         data-decade="<?= esc_attr($document_decade) ?>"
         data-fii-death-type="<?= esc_attr($document_death_type->term_id) ?>"
         data-establishment-type="<?= esc_attr($document_establishment_type) ?>">
	<a href="<?= $document_upload ?>" target="_blank">
			<h3><?= get_the_title( $document_establishment_id ) ? get_the_title( $document_establishment_id ) : '' ?></h3>
			<div class="tile-details">
				<table>
					<tr>
						<td colspan="2">
              <strong>
                <?php
                if ( $document_establishment_type ) {
                  echo get_term_field( 'name', $document_establishment_type, 'establishment-type' );
                }
                ?>
							</strong>
            </td>
					</tr>
					<tr>
						<td>Date of death:</td>
						<td><?php echo $death_date; ?></td>
					</tr>
					<tr>
						<td>Cause:</td>
						<td><?= $document_death_type ? $document_death_type->name : null ?></td>
					</tr>
          <tr>
            <td>Gender:</td>
						<td><?= get_post_meta($id, 'fii-gender', true) == 'm' ? 'Male' : 'Female' ?></td>
          </tr>
					<tr>
            <td>Age:</td>
						<td><?= get_post_meta($id, 'fii-age', true) ?></td>
					</tr>
					<tr>
						<td>On website:</td>
						<td><?= $document_date ?></td>
					</tr>
				</table>
			</div>
	</a>
</article>
