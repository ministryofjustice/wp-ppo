<?php
/**
 * Represents the content details view for Sidejump administration dashboard.
 *
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 */
?>
<script>
    jQuery(function() {

        jQuery('#tree1').checkboxTree().each(function() {
            eval(jQuery(this).html());
        });
    });
    // For starting zip process using ajax 
    function start_zip_process()
    {
        // checking if atleast 1 checkbox is checked
        if (jQuery("#sync-form input:checkbox:checked").length > 0)
        {

            var themeslist = new Array();
            var pluginslist = new Array();
            var newmediafiles = new Array();
            var commonmediafiles = new Array();
            var syncType = jQuery("#type").val();
            
             // if any plugin is selected then automatically checked db option
            if( jQuery('input:checkbox:checked.pname').length > 0){
                jQuery('#db').prop('checked', true);
            } 
            var syncdb = jQuery("#db:checked").val();
            if (typeof syncdb !== "undefined") {
                wps_db = syncdb;
            } else {
                wps_db = 0;
            }
           
            jQuery('input:checkbox:checked.tname').each(function() {
                themeslist.push(jQuery(this).val());
            });

            jQuery('input:checkbox:checked.pname').each(function() {
                
                pluginslist.push(jQuery(this).val());
               
            });

            jQuery('input:checkbox:checked.nmname').each(function() {
                newmediafiles.push(jQuery(this).val());
            });

            jQuery('input:checkbox:checked.cmname').each(function() {
                commonmediafiles.push(jQuery(this).val());
            });
            jQuery("#loaderImage").show();
            var data = {
                action: 'sync_process',
                sid: <?php echo ($_REQUEST['sid']) ? $_REQUEST['sid'] : 0 ?>,
                tid: <?php echo ($_REQUEST['tid']) ? $_REQUEST['tid'] : 0 ?>,
                wps_theme: themeslist,
                wps_plugin: pluginslist,
                wps_new_media: newmediafiles,
                wps_common_media: commonmediafiles,
                wps_db_value: wps_db,
                wps_sync_type: syncType
            };

            jQuery.post(ajaxurl, data, function(response) {
                jQuery("#loaderImage").hide();
                alertify.alert(response);              
                // alert(response);
            });

        } else {

            // alert dialog
            alertify.alert("<?php _e('Please make a selection first.',$this->plugin_slug); ?>");
            return false;
        }

        return true;
    }

</script>
<?php
// Setting default value
$targetInstanceId = 0;

// Domain name used for localization
$domain = $this->plugin_slug;

// Checking source and target instance id requested by Admin
if ($_REQUEST['sid'] != 0 && $_REQUEST['tid'] != 0) {
    $sourceInstanceId = $_REQUEST['sid'];
    $targetInstanceId = $_REQUEST['tid'];
} else {
// If no target instance id provided then show error page and exit
    $errMessage = __("Please provide both instances (Source or Target instance is missing).", $domain);
    include_once(plugin_dir_path(dirname(__FILE__)) . '/views/error-page.php');
    exit;
}

// Creating instance for using WP_Sync_Admin class
$wpsInstance = new WP_Sync_Admin();

// Fetch details  of source instance
$sourceDetails = $wpsInstance->wps_get_instances($sourceInstanceId);
$targetDetails = $wpsInstance->wps_get_instances($targetInstanceId);

// Folders to be fetched for Source Instance
$folders = array('themes', 'plugins');
// Getting content from Source Instance
$sourceContent = wps_source_content($sourceDetails, $folders);
$sourceMediaContent = wps_source_media_content($sourceInstanceId, $targetInstanceId);
?>
<?php if ($sourceContent): ?>
    <div class="wrap" id= "wpsync-content">
        
        <h2 class="logo"><?php _e('Sidejump'); ?></h2>  
        <h2><?php printf(__("Push %s files and tables to %s", $domain), $sourceDetails['0']->target_name, $targetDetails['0']->target_name); ?></h2>

        <div class="update-nag"><small><?php _e("To avoid potentially catastrophic file and database corruption, Sidejump does not sync WP core files.", $domain); ?></small></div>
        

        <div id="main">
            <form action="" method="post" id="sync-form">
                <div class="tab-wrapper">
                    <div id="TabbedPanels1" class="TabbedPanels">
                        <ul class="TabbedPanelsTabGroup">
                            <li class="TabbedPanelsTab" tabindex="0"><?php _e('Themes', $domain); ?></li>
                            <li class="TabbedPanelsTab" tabindex="0"><?php _e('Plugins', $domain); ?></li>
                            <li class="TabbedPanelsTab" tabindex="0"><?php _e('Media', $domain); ?></li>
                            <li class="TabbedPanelsTab" tabindex="0"><?php _e('Database', $domain); ?></li>
                        </ul>
                        <div class="TabbedPanelsContentGroup">
                           
                            <div class="TabbedPanelsContent">

                                <div class="themes-list">
                                
                                    <div class="update-nag">
                                        <small>
                                            <?php printf(__("In case of errors and unintended consequences, Sidejump will create a backup of the %s instance before synchronization. Theme, plugin, and media files are stored in their respective WP directories (as zipped files), and database backup files are stored as gzipped SQL files here: wp-content/plugins/sidejump/admin/dbbackups/", $domain), $targetDetails['0']->target_name); ?>
                                        </small>
                                    </div>

                                    <table>
                                        <?php
                                        // Getting listing of all themes available
                                        $themesList = $sourceContent['themes'];
                                        if ($themesList):
                                            ?>
                                            <div id="head-subtext">
                                                <h4><span><input type="checkbox" id="allthemes" value="0"/></span><?php _e('Select all', $domain) ?></h4>
                                            </div>                    
                                            <?php foreach ($themesList as $themeName): ?>
                                                <tr>
                                                    <td><input type="checkbox" class="tname" name="wps_theme[]" value="<?php _e($themeName); ?>" /> </td>
                                                    <td>
                                                        <?php _e($themeName, $domain); ?> 

                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr> <td><?php _e('No themes available', $domain); ?><td></tr>
                                        <?php endif; ?>
                                    </table>

                                </div>

                            </div>
                           
                            <div class="TabbedPanelsContent">

                                <div class="plugins-list">
                                    
                                    <div class="update-nag">
                                        <small>
                                            <?php printf(__("Since most plugins are database-dependent, when you synchronize plugin files, you will also synchronize the database. As such, when you click \"sync,\" Sidejump will overwrite the contents of the %s database with the contents of the %s database. The only exceptions follow:", $domain), $targetDetails['0']->target_name, $sourceDetails['0']->target_name); ?>
                                        </small>
                                        <small class="list">
                                            <?php _e("The site and home URL options, Sidejump tables, and primary admin user (assumed to be ID #1) are never overwritten.", $domain); ?>
                                        </small>
                                        <small class="list">
                                            <?php printf(__("Please proceed with caution. Users accept all liability for use according to these %s terms", $domain), "<a href=\"http://sidejump.net/terms/\" target=\"_blank\">"); ?></a>.
                                        </small>
                                        <small>
                                            <?php printf(__("In case of errors and unintended consequences, Sidejump will create a backup of the %s instance before synchronization. Theme, plugin, and media files are stored in their respective directories (as zipped files), and database backup files are stored as gzipped SQL files here: wp-content/plugins/sidejump/admin/dbbackups/", $domain), $targetDetails['0']->target_name); ?>
                                        </small>
                                    </div>

                                    <table>
                                        <?php
                                        // Getting listing of all plugins available   
                                        $pluginsList = $sourceContent['plugins'];
                                        if ($pluginsList):
                                            ?>           
                                            <div id="head-subtext">
                                                <h4><input type="checkbox" id="allplugins" value="0"/></span><?php _e('Select all', $domain) ?></h4>
                                            </div>          


                                            <?php
                                            foreach ($pluginsList as $pluginName):
                                                if ($pluginName == 'sidejump') {
                                                    continue;
                                                }
                                                ?>

                                                <tr>
                                                    <td><input type="checkbox" class="pname" name="wps_plugin[]" value="<?php _e($pluginName); ?>" /> </td>
                                                    <td>
                                                        <?php _e($pluginName, $domain); ?> 

                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr> <td><?php _e('No plugins available', $domain); ?><td></tr>                                   
                                        <?php endif; ?>
                                    </table>

                                </div>

                            </div>

                            <div class="TabbedPanelsContent">

                                <div class="media-list">

                                    <div id="tabs-1">
                                        <?php if ($sourceMediaContent): ?>

                                            <ul id="tree1">

                                                <div class="update-nag">
                                                    <small>
                                                        <?php printf(__("These files exist on %s but not on %s.", $domain), $sourceDetails['0']->target_name, $targetDetails['0']->target_name); ?>
                                                    </small>
                                                </div>

                                                <li id="newmedia"><input type="checkbox" class="nmname"  value="yes" /><label><?php _e("New Media Files", $domain); ?></label>    
                                                    <ul>
                                                        <?php foreach ($sourceMediaContent['newFiles'] as $key => $value) : ?>

                                                            <li><input type="checkbox" class="nmname"  value="<?php echo 'uploads/' . $key; ?>" /><label><?php _e($key); ?></label>  
                                                                <ul>

                                                                    <?php foreach ($value as $key2 => $value2) : ?> 
                                                                        <li><input type="checkbox" class="nmname"  value="<?php echo 'uploads/' . $key . '/' . $key2; ?>" /><label><?php _e($key2); ?></label> 
                                                                            <?php if ($value2) : ?>    
                                                                                <ul>
                                                                                    <?php foreach ($value2 as $files) : ?> 
                                                                                        <?php
                                                                                        if ($key2 === basename($files)) {
                                                                                            continue;
                                                                                        }
                                                                                        ?>
                                                                                       
                                                                                        <li><input type="checkbox" class="nmname"  value="<?php _e($files); ?>" /><label><?php _e(basename($files)); ?></label> 
                                                                                        
                                                                                            <?php endforeach; ?>
                                                                                </ul>
                                                                            <?php else : ?>

                                                                                <ul><?php _e('No files found', $domain); ?></ul>

                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                </ul>
                                                            <?php endforeach; ?>

                                                    </ul>    
                                                </li>

                                                <div class="update-nag">
                                                    <small>
                                                        <?php printf(__("These files have a more recent timestamp on %s than on %s.", $domain), $sourceDetails['0']->target_name, $targetDetails['0']->target_name); ?>
                                                    </small>
                                                </div>

                                                <li><input type="checkbox" class="cmname"  value="yes"/><label><?php _e("Updated Media Files", $domain); ?></label>    

                                                    <ul>
                                                        <?php foreach ($sourceMediaContent['commonRecentFiles'] as $key => $value) : ?>

                                                            <li><input type="checkbox" class="cmname"  value="<?php _e($key); ?>"/><label><?php _e($key); ?></label>  
                                                                <ul>

                                                                    <?php foreach ($value as $key2 => $value2) : ?> 
                                                                        <li><input type="checkbox" class="cmname"  value="<?php _e($key2); ?>" /><label><?php _e($key2); ?></label> 
                                                                            <?php if ($value2): ?>    
                                                                                <ul>
                                                                                    <?php foreach ($value2 as $files) : ?> 
                                                                                        <?php
                                                                                        if ($key2 === basename($files)) {
                                                                                            continue;
                                                                                        }
                                                                                        ?>
                                                                                        <li><input type="checkbox" class="cmname"  value="<?php _e($files); ?>"/><label><?php _e(basename($files)); ?></label> 
                                                                                        <?php endforeach; ?>
                                                                                </ul>
                                                                            <?php else : ?>

                                                                                <ul><?php _e('No files found', $domain); ?></ul>

                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                </ul>
                                                            <?php endforeach; ?>

                                                    </ul> 
                                                </li>

                                            <?php else : ?> 
                                                <div><?php _e('No media content found.', $domain); ?></div>  
                                            <?php endif; ?>  
                                    </div>
                                </div>
                            </div>

                            <div class="TabbedPanelsContent"><div class="database-sync">
                                    <div class="syncdb">

                                        <div class="update-nag">
                                            <small>
                                                <?php printf(__('When you click "Sync" Sidejump will overwrite the contents of the %s database with the contents of the %s database. The only exceptions follow:', $domain), $targetDetails['0']->target_name, $sourceDetails['0']->target_name); ?>
                                            </small>
                                            <small class="list">
                                                <?php _e("The site and home URL options, Sidejump tables, and primary admin user (assumed to be ID #1) are never overwritten.", $domain); ?>
                                            </small>
                                            <small class="list">
                                                <?php printf(__("Please proceed with caution. Users accept all liability for use according to these %s terms", $domain), "<a href=\"http://sidejump.net/terms/\" target=\"_blank\">"); ?></a>.
                                            </small>
                                            <small>
                                                <?php printf(__("In case of errors and unintended consequences, Sidejump will create a backup of the %s instance before synchronization. Theme, plugin, and media files are stored in their respective directories (as zipped files), and database backup files are stored as gzipped SQL files here: wp-content/plugins/sidejump/admin/dbbackups/", $domain), $targetDetails['0']->target_name); ?>
                                            </small>
                                        </div>

                                        <input type="checkbox" name="db" id="db" value="1" /> <label><?php _e('Database', $domain); ?></label>

                                    </div>

                                </div></div>

                        </div>  
                    </div>
                </div>  
                <div class="clear"></div>

                <div class="sync-btn">
                    <?php
                    // checking for push/pull case
                    $siteURL = rtrim(get_site_url(), "/");
                    $sourceURL = rtrim($sourceDetails['0']->target_url, "/");
                    if ($siteURL == $sourceURL) {
                        $syncType = 'push';
                    } else {
                        $syncType = 'pull';
                    }
                    ?>
                    <input type="hidden" id="type" name="type" value="<?php echo $syncType; ?>" />
                    <input type="button" name ="sync" id="sync" value="Sync" onclick="start_zip_process()" class="button-primary"/>
                    <span id="loaderImage" style="display:none">
                        <img src="<?php echo plugins_url('sidejump/admin/assets/images/ajax-sync-loader.gif'); ?>"/>
                    </span>
                </div>
            </form>
        </div></div>
    <?php
else:

    $errMessage = __("Source instance details not found.Is the plugin activated on target instance?", $domain);
    include_once(plugin_dir_path(dirname(__FILE__)) . '/views/error-page.php');
    exit;
    ?>

<?php endif; ?>
<script type="text/javascript">
    var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
</script>



