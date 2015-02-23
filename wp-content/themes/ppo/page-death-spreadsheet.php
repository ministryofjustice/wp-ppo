<?php get_template_part('templates/page', 'header'); ?>

<?php $table_name = $wpdb->prefix . 'death_spreadsheet'; ?>

<?php $columns = array("case", "death", "deceased_surname", "type", "establishment", "location", "sex", "age_group", "ethnic_origin", "stage", "webid"); ?>
<?php foreach($columns as $column): ?>
  <?php $results = $wpdb->get_results("SELECT DISTINCT `$column` FROM $table_name"); ?>
  <?php if($results): ?>
    <label for="columns"><?= $column ?></label>
    <select name="columns">
    <?php foreach($results as $result): ?>
      <option value="<?= $result->$column ?>"><?= $result->$column ?></option>
    <?php endforeach; ?>
    </select>
  <?php endif; ?>
<?php endforeach; ?>

<?php $results = $wpdb->get_results($wpdb->prepare("SELECT `case`, death, deceased_surname, type, establishment, location, sex, age_group, ethnic_origin, stage, webid FROM $table_name WHERE sex != %s", $sex)); ?>
<?php if($results): ?>
  <table class="table table-hover">
  <thead>
    <th>Case</th>
    <th>Death</th>
    <th>Deceased Surname</th>
    <th>Type</th>
    <th>Establishment</th>
    <th>Location</th>
    <th>Sex</th>
    <th>Age Group</th>
    <th>Ethnic Origin</th>
    <th>Stage</th>
    <th>WebID</th>
  </thead>
  <?php foreach($results as $result): ?>
    <tr>
      <?php foreach($result as $r): ?>
        <td><?= $r ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
