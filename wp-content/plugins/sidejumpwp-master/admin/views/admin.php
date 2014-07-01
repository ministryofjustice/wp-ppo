<?php
/**
 * Represents the main view for Sidejump administration dashboard.
 *
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 */
?>
<script>
    jQuery(function() {
        jQuery("#source-list").change(function() {
            var sid = jQuery(this).val();
            var data = {
                action: 'target_options',
                sid: sid
            };
            jQuery.post(ajaxurl, data, function(response) {
                jQuery("#target-list").html(response);

            });

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

// Fetch listing of target instances
$targetAdded = $wpsInstance->wps_get_instances();
?>

<div class="wrap" id="wpsync">

    <h2 class="logo"><?php _e('Sidejump'); ?></h2>

    <form method="get" action="admin.php" class="syncform">

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">   
                        <?php _e('Source instance', $domain); ?>
                    </th>
                    <td>
                        <?php if ($targetAdded): ?>
                            <select name="sid" id="source-list">
                                <option value="0"><?php _e('Select source', $domain); ?></option>
                                <?php foreach ($targetAdded as $target): ?>

                                    <option value="<?php _e($target->id); ?>" ><?php _e($target->target_name, $domain); ?></option>
                                <?php endforeach; ?>
                            </select> 
                        <?php else: ?>
                            <?php _e('You haven\'t added any instances yet.', $domain); ?>
                        <?php endif; ?>   
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Target instance', $domain); ?>
                    </th>
                    <td>
                        <?php if ($targetAdded): ?>
                            <select name="tid" id="target-list">
                                <option value="0"><?php _e('Select target', $domain); ?></option>
                            </select> 
                        <?php else: ?>
                            <?php _e('You haven\'t added any instances yet.', $domain); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>
            <input type="hidden" name="page" value="wpsync-details" />
            <input type="submit"  value="<?php _e('Get Details', $domain); ?>" class="button-primary button">
        </p>

    </form>

</div>
