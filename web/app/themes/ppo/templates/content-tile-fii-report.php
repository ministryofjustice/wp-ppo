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
$individual_name = get_post_meta($id, 'fii-name', true);
if ($individual_name == "") $individual_name = "Individual at $establishment_name";

$death_types = get_the_terms($id, 'fii-death-type');
if (!is_wp_error($death_types) && count($death_types) > 0) {
    $death_type = $death_types[0];
} else {
    $death_type = false;
}

$death_date = get_post_meta($id, 'fii-death-date', true);


$action_plan = (get_post_meta($id, 'show-action-plan', true) == 'on');
if ($action_plan) {
    $action_plan_document = get_post_meta($id, 'action-plan-document', true);

    $action_plan_label = get_post_meta($id, 'action-plan-label', true);
    if (empty($action_plan_label)) {
        $action_plan_label = 'Action Plan';
    }
}

?>
<article id="<?= 'doc-' . $id ?>" class="<?= esc_attr($document_type) ?>">
    <div class="tile-details">
        <h3 class="card-title">
            <a href="<?= $document_upload ?>" target="_blank">
                <?= $individual_name ?>
            </a>
        </h3>
        <strong><?= $establishment_name ?></strong><br /><?= $establishment_type_name ?>
        <div class="tile-published-date">Published: <?= $document_date ?></div>
        <table>
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
        </table>
        <nav class="report-links">
            <ul>
                <li><a href="<?= $document_upload ?>" target="_blank">PPO Report</a> <?= file_meta($document_upload) ?>
                </li>
                <?php if ($action_plan): ?>
                    <li><a href="<?= $action_plan_document ?>"
                           target="_blank"><?= $action_plan_label ?></a> <?= file_meta($action_plan_document) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</article>
