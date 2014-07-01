<?php
/**
 * Represents the configured sites listing view for Sidejump administration dashboard.
 *
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 */
?>
<script>
    jQuery(function() {

        // add multiple select / deselect functionality
        jQuery("#chkall").click(function() {
            jQuery('.stag').attr('checked', this.checked);
        });

        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        jQuery(".stag").click(function() {

            if (jQuery(".stag").length == jQuery(".stag:checked").length) {
                jQuery("#chkall").attr("checked", "checked");
            } else {
                jQuery("#chkall").removeAttr("checked");
            }

        });
    });

    // For starting clean up process using ajax 
    function start_cleanup_process()
    {
        // checking if atleast 1 checkbox is checked
        if (jQuery("#cleanup-form input:checkbox:checked").length > 0)
        {
            // confirm dialog
            alertify.confirm("<?php _e('Delete all backup files linked with selected synctags ?',$this->plugin_slug); ?>", function(e) {
                if (e) {
                    jQuery("#loaderImage").show();
                    var synctag_list = new Array();

                    jQuery('input:checkbox:checked.stag').each(function() {

                        synctag_list.push(jQuery(this).val());

                    });

                    var data = {
                        action: 'clean_process',
                        wps_sync_tags: synctag_list
                    };

                    jQuery.post(ajaxurl, data, function(response) {
                        jQuery("#loaderImage").hide();
                        alertify.alert(response);
                        
                        // reload page after 4 secs
                        window.setTimeout(function(){
                            window.location.href=window.location.href
                        },4000)
                      
                    });

                } else {

                    return false;
                }
            });


        } else {

            // alert dialog
            alertify.alert("<?php _e('Please make a selection first.', $this->plugin_slug); ?>");

            return false;
        }

    }

</script>
<?php
// Domain name used for localization
$domain = $this->plugin_slug;

// Creating instance for using WP_Sync_Admin class
$wpsInstance = new WP_Sync_Admin();

// Checking $_POST data
if (isset($_POST['wps_action']) && $_POST['wps_action'] == 'delete') {
    // Removing target instance
    $wpsInstance->wps_remove_target_instance($_POST);
}
// Fetch listing of saved sync processes
$syncProcessInfo = $wpsInstance->wps_get_sync_tracked_info();
?>

<div class="wrap" id="wpsync">
    
    <h2 class="logo"><?php _e('Sidejump', $domain); ?></h2>      
    
    <h2><?php _e('Remove old backup files from your server.', $domain); ?></h2>

    <?php if ($syncProcessInfo): ?>

        <div class="update-nag">
            <small>
                <?php printf(__('The files are listed in descending order from least to most recent. The numerical portions of the filenames (eg., "1398026124") are Unix timestamps, which can be converted $s here', $domain), '<a href="http://www.onlineconversion.com/unix_time.htm" target="_blank">'); ?>
            </small>
        </div>

        <form action="" method="post" id="cleanup-form">
            <table class="wp-list-table widefat fixed">
            <thead>
                <tr>
                   <th scope="row"><input type="checkbox" id="chkall" /> <?php _e('Check All', $domain); ?></th>
                   <th scope="row"><?php _e('Filename', $domain); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($syncProcessInfo as $sync): ?>
                <tr>
                    <th scope="row"><input type="checkbox" class="stag" value="<?php echo $sync->sync_tagname; ?>"/></th>
                    <td><?php echo $sync->sync_tagname; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                   <th scope="row"><input type="checkbox" id="chkall" /><?php _e('Check All', $domain); ?></th>
                   <th scope="row"><?php _e('Filename', $domain); ?></th>
                </tr>
            </tfoot>
            </table>

            <input type="button" name ="del-sync-tag" id="del-sync-tag" value="Delete" onclick="start_cleanup_process()" class="button-primary"/>
            <span id="loaderImage" style="display:none"><img src="<?php echo plugins_url('sidejump/admin/assets/images/ajax-sync-loader.gif'); ?>"/></span>
        </form>

    <?php else: ?>

        <div class="update-nag">
            <?php _e('Sidejump hasn\'t created any backup files yet.', $domain); ?>
        </div>

    <?php endif; ?>

</div>
