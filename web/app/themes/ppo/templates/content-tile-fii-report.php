<?php

$id = get_the_ID();
$document_type = get_the_terms($id, 'document_type');
$document_type = $document_type[0]->slug;

$document_date = get_post_meta($id, 'document-date', true);
$document_upload = get_post_meta($id, 'document-upload', true);

// Fatal Incident report specific
$establishment_id = get_post_meta($id, 'fii-establishment', true);
$establishment_name = get_the_title($establishment_id);
$establishment_type = get_post_meta($establishment_id, 'establishment-type', true);
$establishment_type_name = get_term_field('name', $establishment_type, 'establishment-type');

$death_types = get_the_terms( $id, 'fii-death-type' );
if ( !is_wp_error($death_types) && count($death_types) > 0 ) {
  $death_type = $death_types[0];
} else {
  $death_type = false;
}

$death_date = get_post_meta($id, 'fii-death-date', true);

?>
<article id="<?= 'doc-' . $id ?>" class="<?= esc_attr($document_type) ?>">
	<a href="<?= $document_upload ?>" target="_blank">
			<h3><?= $establishment_name ?></h3>
			<div class="tile-details">
        <div class="tile-published-date">Published: <?= $document_date ?></div>
				<table>
					<tr>
						<td colspan="2">
              <strong><?= $establishment_type_name ?></strong>
            </td>
					</tr>
					<tr>
						<td>Date of death:</td>
						<td><?php echo $death_date; ?></td>
					</tr>
					<tr>
						<td>Cause:</td>
						<td><?= $death_type ? $death_type->name : null ?></td>
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
