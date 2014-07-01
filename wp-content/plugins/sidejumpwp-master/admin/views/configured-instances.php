<?php
/**
 * Represents the configured sites listing view for Sidejump administration dashboard.
 *
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 */
// Domain name used for localization
$domain = $this->plugin_slug;
// Response variable for showing messages
$response = '';
// Creating instance for using WP_Sync_Admin class
$wpsInstance = new WP_Sync_Admin();

// Checking $_POST data
if (isset($_POST['wps_action']) && $_POST['wps_action'] == 'delete') {
    // Removing target instance
    $wpsInstance->wps_remove_target_instance($_POST);
}
// Fetch listing of target instances
$targetAdded = $wpsInstance->wps_get_instances();
?>
<div class="wrap" id="wpsync">
    
    <h2 class="logo"><?php _e('Sidejump', $domain); ?></h2>      
    
    <h2><?php _e('WP Instances', $domain); ?></h2>

    <?php if ($targetAdded): ?>
        
        <table class="wp-list-table widefat fixed">
        <thead>
            <tr>
               <th scope="row"><?php _e('Name', $domain); ?></th>
               <th scope="row"><?php _e('URL', $domain); ?></th>
               <th scope="row"><?php _e('Action', $domain); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($targetAdded as $target): ?>
            <tr>
                <th scope="row"><?php _e($target->target_name, $domain); ?></th>
                <td><?php _e($target->target_url, $domain); ?></td>
                <td>
                    <form method="post" action="">
                        <a class="button-primary" href="admin.php?page=new-target-instance&tid=<?php _e($target->id); ?>" ><?php _e('Edit', $domain); ?></a>
                        <input type="hidden" name="target" value=<?php _e($target->id); ?> />
                        <input type="hidden" name="wps_action" value="delete" >
                        <input type="submit"  value="<?php _e('Delete', $domain); ?>" class="button-primary">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
               <th scope="row"><?php _e('Name', $domain); ?></th>
               <th scope="row"><?php _e('URL', $domain); ?></th>
               <th scope="row"><?php _e('Action', $domain); ?></th>
            </tr>
        </tfoot>
        </table>

    <?php else: ?>

        <div class="update-nag">
            <?php _e('You haven\'t added any instances yet.', $domain); ?>
        </div>

    <?php endif; ?>

</div>
