<?php
/**
 * Represents the add new wp site details view for Sidejump administration dashboard.
 *
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 */
?>
<script>
    // Making instance as local
    jQuery(function() {
        jQuery('#set-local').change(function() {
            if (jQuery(this).is(':checked')) {
                var localURL = '<?php echo site_url() ?>';
                jQuery('#target_name').val('Local');
                jQuery('#target_url').val(localURL);

            } else {
                jQuery('#target_name').val("");
                jQuery('#target_url').val("");


            }

        });
    });

</script>
<?php
// Domain name used for localization
$domain = $this->plugin_slug;
// Response variable for showing messages
$response = '';
// Creating instance for using WP_Sync_Admin class
$wpsInstance = new WP_Sync_Admin();

$targetId = 0;
// Checking target instance id requested by Admin
if (isset($_REQUEST['tid'])) {
    $targetId = $_REQUEST['tid'];
}

// Checking $_POST data
if (isset($_POST['wps_action'])) {         
    // Adding target instance
    $addTargetMsg = $wpsInstance->wps_add_target_instance($_POST);
    // get messages
    switch ($addTargetMsg) {
        case 'failed' :
            $response = __('Target instance not saved.', $domain);
            break;
        case 'exists' :
            $response = __('Target instance already exists.', $domain);
            break;
         case 'success' :
            $response = __('Target instance saved successfully.', $domain);
            break;
        default :
            $response = '';
            break;
    }
}

// Fetch details  of target instance
$targetDetails = $wpsInstance->wps_get_instances($targetId);
$unserializeFtpData = array();
if ($targetDetails['0']) {
    $unserializeFtpData = maybe_unserialize($targetDetails['0']->target_ftp_details);
}
?>
<div class="wrap" id="wpsync">
    
  <h2 class="logo">
    <?php _e('Sidejump', $domain); ?>
  </h2>
  
  <h2>
    <?php ($targetId) ? _e('Edit WP instance.', $domain) : _e('Add a new WP instance.', $domain); ?>
  </h2>
  
  <div id="setting-error-settings_updated" class="<?php echo ($response) ? 'updated settings-error' : '' ; ?>">
    <p><strong><?php _e($response, $domain); ?></strong></p>
  </div>

   <form method="post" action="" onsubmit="return validate_details();">

      <table class="form-table">
         <tbody>
            <tr>
               <th scope="row">
                  <label for="target_name"><?php _e('Instance Name', $domain); ?> *</label>
               </th>
               <td>
                  <input type="text"  name="target_name" id="target_name" class="regular-text" value="<?php ($targetId) ? _e($targetDetails['0']->target_name, $domain) : ''; ?>">
                  <input type="checkbox" value="0" id="set-local"> <?php _e('Local', $domain); ?>
                  
                  <p class="description">
                     <?php _e('Please enter your instance\'s name (eg., "Development," "Staging," "Production." If you check "Local," Sidejump will insert your current instance\'s name and url. ',$domain); ?>
                  </p>
               </td>
            </tr>
            <tr>
               <th scope="row">
                  <label for="target_url"><?php _e('Instance URL', $domain); ?> *</label>
               </th>
               <td>
                  <input type="text"  name="target_url" id="target_url" class="regular-text" value="<?php ($targetId) ? _e($targetDetails['0']->target_url, $domain) : ''; ?>" />
                  
                  <p class="description"><?php _e('Please enter your instance\'s URL.',$domain); ?></p>
               </td>
            </tr>
            <tr>
               <th scope="row">
                  <label for="ftp_host"><?php _e('FTP Hostname *', $domain); ?></label>
               </th>
               <td>
                  <input type="text"  name="ftp_host" id="ftp_host" class="regular-text" value="<?php ($targetId) ? _e($unserializeFtpData['ftp_host'], $domain) : ''; ?>">
                  
                  <p class="description"><?php _e('Please enter your instance\'s FTP hostname.',$domain); ?></p>
               </td>
            </tr>
            <tr>
               <th scope="row">
                  <label for="ftp_uname"><?php _e('FTP Username*', $domain); ?></label>
               </th> 
               <td>
                  <input type="text"  name="ftp_uname" id="ftp_uname" class="regular-text" value="<?php ($targetId) ? _e($unserializeFtpData['ftp_uname'], $domain) : ''; ?>">
                  
                  <p class="description"><?php _e('Please enter your instance\'s FTP username.',$domain); ?></p>
               </td>
            </tr>
            <tr>
               <th scope="row">
                  <label for="ftp_pswd"><?php _e('FTP Password *', $domain); ?></label>
               </th>
               <td>
                  <input type="password"  name="ftp_pswd" id="ftp_pswd" class="regular-text" value="<?php ($targetId) ? _e($unserializeFtpData['ftp_pswd'], $domain) : ''; ?>">
                  
                  <p class="description"><?php _e('Please enter your instance\'s FTP password.',$domain); ?></p>
               </td>
            </tr>
            <tr>
               <th scope="row">
                  <label for="ftp_root_path"><?php _e('Remote Directory Path *', $domain);  ?></label>
               </th>
               <td>
                  <input type="text"  name="ftp_root_path" id="ftp_root_path" class="regular-text" value="<?php ($targetId) ? _e($unserializeFtpData['ftp_root_path'], $domain) : ''; ?>">
                  
                  <p class="description"><?php _e('Please enter your instance\'s WP root directory path. (e.g., /var/www/wordpress). If WordPress is installed in your root directory, just type "/". ',$domain); ?></p>
               </td>
            </tr>
         </tbody>
      </table>

      <input type="hidden"  name="id" value="<?php _e($targetId); ?>" >
      <input type="hidden" name="wps_action" value="add" >
      <p class="submit">
         <input type="submit"  value="<?php ($targetId) ? _e('Update Instance', $domain) : _e('Add Instance', $domain); ?>" class="button-primary button">
      </p>

   </form>

</div>
