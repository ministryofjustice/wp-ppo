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

// Death date
$death_date = get_post_meta($id, 'fii-death-date', true);
$death_date_timestamp = strtotime(str_replace("/", "-", $death_date));
$death_date = date("j M Y",$death_date_timestamp);

$anon_date = "01-03-2015"; // Before the 1st of March 2015, names aren't to be displayed

// Age bracket
$age_bracket = get_post_meta($id, 'fii-age', true);

if (strtotime($anon_date) <= $death_date_timestamp) {
    // On or after March 1st, 2015

    // Name information
    $individual_surname = get_post_meta($id, 'fii-name', true);
    $individual_forenames = get_post_meta($id, 'fii-forenames', true);
    $initialise = get_post_meta($id, 'fii-initialise', true);

    // Split forenames into array
    $individual_name_array = preg_split("/\s+/", $individual_forenames);
    $individual_initial_array = [];
    foreach ($individual_name_array as $initial) {
        $individual_initial_array[] = mb_substr(strtoupper($initial), 0, 1);
    }

    // Set display name to that selected
    if ($initialise == "none") {
        $individual_display_forenames = $individual_forenames;
    } elseif ($initialise == "middle") {
        unset($individual_initial_array[0]);
        $individual_display_forenames = $individual_name_array[0]." ".implode("",$individual_initial_array);
    } else {
        $individual_display_forenames = implode("",$individual_initial_array);
    }
} else {
    // Before March 1st, 2015
    $individual_surname = "";
}

if ($age_bracket == "Under 18") {
    // Don't display names of minors
    $individual_surname = "";
}

if (trim($individual_surname) == "") {
    // Surname not recorded, or, individual is minor, or, died < 01 Mar 15
    $individual_name = "Individual at $establishment_name";
} elseif (trim($individual_forenames) == "") {
    // Only surname is recorded
    $individual_name = $individual_surname;
} else {
    $individual_name = $individual_surname.", ".$individual_display_forenames;
}

$death_types = get_the_terms($id, 'fii-death-type');
if (!is_wp_error($death_types) && count($death_types) > 0) {
    $death_type = $death_types[0];
} else {
    $death_type = false;
}

$inquest_occurred = get_post_meta($id, 'fii-inquest-occurred', true);
$inquest_date = get_post_meta($id, 'fii-inquest-date', true);
if ($inquest_date != "") {
    $inquest_date_timestamp = strtotime(str_replace("/", "-", $inquest_date));
    $inquest_date = date("j M Y",$inquest_date_timestamp);
}

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
        <div class="tile-published-date">Published: <?= date("j F Y",strtotime(str_replace("/", "-", $document_date )))?></div>
        <table>
            <tr>
                <td>Date of death:</td>
                <td><?php echo $death_date; ?></td>
            </tr>
            <?php if ($inquest_occurred == "no") { ?>
            <tr>
                <td>Date of inquest:</td>
                <td>Not held</td>
            </tr>
            <?php } elseif ($inquest_date != "") { ?>
            <tr>
                <td>Date of inquest:</td>
                <td><?php echo $inquest_date; ?></td>
            </tr>
            <?php } ?>
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
                <td><?php echo $age_bracket; ?></td>
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
