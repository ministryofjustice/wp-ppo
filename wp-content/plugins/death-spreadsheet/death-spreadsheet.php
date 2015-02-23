<?php

/**
 * Plugin Name: Death Spreadsheet
 * Plugin URI:
 * Description: Import CSV file for death spreadsheet table
 * Version: 1.0.0
 * Author: Toby Schrapel
 * Author URI: http://tobyschrapel.com
 */

function load_scripts() {
  wp_enqueue_script( 'script-name', plugins_url() . '/death-spreadsheet/assets/js/main.js', array(), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'load_scripts' );


function death_spreadsheet_plugin_settings() {
    add_menu_page('Death Spreadsheet', 'Death Spreadsheet', 'administrator', 'death_spreadsheet_settings', 'death_spreadsheet_display_settings');
}
add_action('admin_menu', 'death_spreadsheet_plugin_settings');


function add_query_vars_filter($vars){
  $vars[] = "sex";
  return $vars;
}
add_filter('query_vars', 'add_query_vars_filter');


function death_spreadsheet_install() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'death_spreadsheet';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    `case` text NOT NULL,
    death text NOT NULL,
    deceased_surname text NOT NULL,
    type text NOT NULL,
    establishment text NOT NULL,
    location text NOT NULL,
    sex text NOT NULL,
    age_group text NOT NULL,
    ethnic_origin text NOT NULL,
    stage text NOT NULL,
    webid text,
    UNIQUE KEY id (id)
  ) $charset_collate";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
register_activation_hook(__FILE__,'death_spreadsheet_install');


function import_csv($file) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'death_spreadsheet';

  $sql = "LOAD DATA LOCAL INFILE '$file'
  INTO TABLE $table_name
  FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 ROWS
  (`case`, death, deceased_surname, type, establishment, location, sex, age_group, ethnic_origin, stage, webid)";

  $wpdb->query("TRUNCATE TABLE $table_name");
  $result = $wpdb->query($sql);
}


function death_spreadsheet_display_settings() { ?>
  <h2>Death Spreadsheet</h2>
  <p>Please upload a exported comma seperated CSV file. Any other file formats will be rejected.</p>
  <p><strong>Required Columns:</strong> Case, Death, Deceased Surname, Type, Establishment, Location, Sex, Age, Group, Ethnic Origin, Stage, WebID</p>
  <form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field( plugin_basename( __FILE__ ), 'security' ); ?>
    <table class="form-table">
      <tbody>
        <th scope="row">
          <label for="file">Filename:</label>
        </th>
        <td>
          <input type="file" name="file" id="file">
        </td>
      </tbody>
    </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
  </form>

  <?php
  if(!empty($_FILES) && isset($_FILES['file'])) {
    if( isset( $_POST[ 'security' ] ) && wp_verify_nonce( $_POST[ 'security' ], plugin_basename( __FILE__ ) )   ) {
      $uploadedfile = $_FILES['file'];

      $file = fopen($uploadedfile['tmp_name'],"r");
      $columns = fgetcsv($file);
      $columns = array_map('trim', $columns);
      $columns_hard = array("Case", "Death", "Deceased Surname", "Type", "Establishment", "Location", "Sex", "Age Group", "Ethnic Origin", "Stage", "WebID");
      fclose($file);

      if($columns != $columns_hard) {
        echo "Incorrect column format.";
      } elseif($uploadedfile['type'] != 'text/csv') {
        echo "Incorrect file format.";
      } else {
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if ( $movefile ) {
           import_csv($movefile['file']);
           unlink($movefile['file']);
           echo "Import successful.";
        }
      }
    }
  }
}
?>
