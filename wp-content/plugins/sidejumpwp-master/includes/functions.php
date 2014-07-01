<?php

/**
 * File used for defining common functions that can be used in admin as well public site.
 *
 *
 * @package Sidejump
 * @author  Jenis Patel <jenis.patel@daffodilsw.com>
 */

/**
 * Used for logging Sidejump plugin messages
 *
 * @since  1.0.0
 * @param  string $message : Message need to be logged
 * @return void
 */
function wps_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

/**
 * Used for fetching list of folders for source instances
 *
 * @since    1.0.0
 * @param  string $details : Source instance details
 * @param  array  $folders : Array of folder names to be fetched
 * @return array  $content : Array of content 
 */
function wps_source_content($details, $folders = array()) {

    try {
        // Initialising
        $results = array();
        // checking whether empty or not
        if ($details['0']) {
            // getting source url
            $sourceUrl = $details['0']->target_url;
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : Making remote call and fetching themes / plugins available on source instance");
            foreach ($folders as $foldername) {
                $results[$foldername] = wps_remote_call($sourceUrl, 'wps_list_folders', $foldername);
            }
        }
        // if results found
        if ($results) {
            $content = array();
            // getting list of folder names
            foreach ($results as $fname => $values) {

                if ($values) {
                    $content[$fname] = json_decode($values, true);
                }
            }
            // logging state and return
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : Returning content available on source instance");
            return $content;
        } else {
            // logging state and return
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : No content found on source instance");
            return false;
        }
    } catch (SyncException $se) {
        // on getting exception show error page
        wps_error_page($se->getMessage());
        exit;
    }
}

/**
 * Used for fetching list of media files/folders for source instances
 * which are not present on target and having recent timestamp
 * 
 * @since    1.0.0
 * @param  string $sid : Source instance id
 * @param  array  $tid : Target instance id
 * @return array  $content : Array of content provided by FTP 
 */
function wps_source_media_content($sid, $tid) {
    try {
        // creating instance for using WP_Sync_Admin class
        $wpsInstance = new WP_Sync_Admin();
        // get source/target details 
        $sourceDetails = $wpsInstance->wps_get_instances($sid);
        $targetDetails = $wpsInstance->wps_get_instances($tid);
        // get source and target urls
        $sourceUrl = $sourceDetails['0']->target_url;
        $targetUrl = $targetDetails['0']->target_url;
        // get response for all media files from curl request
        wps_log(WP_Sync_Admin::WPSYNCLOG . " : Making remote call for getting media content");
        $sourceFilesResponse = wps_remote_call($sourceUrl, 'wps_list_media_files', null);
        $targetFilesResponse = wps_remote_call($targetUrl, 'wps_list_media_files', null);
        // decoding response provided by curl request 
        $sourceMediaFiles = json_decode($sourceFilesResponse);
        $targetMediaFiles = json_decode($targetFilesResponse);

        // removing base url 
        $sourceCleanMediaFiles = wps_explode_data($sourceMediaFiles, '/wp-content/');
        $targetCleanMediaFiles = wps_explode_data($targetMediaFiles, '/wp-content/');

        // get new media files present on source instanc
        $newFiles = @array_diff($sourceCleanMediaFiles, $targetCleanMediaFiles);
        // get common files which exist on both source as well as on target
        $commonFiles = @array_intersect($sourceCleanMediaFiles, $targetCleanMediaFiles);

        $encodeCommonFiles = json_encode($commonFiles);
        // get list of media files need to be compare for timestamp
        $sourceCommonFilesTimeInfo = wps_remote_call($sourceUrl, 'wps_compare_media_files', $encodeCommonFiles);
        $targetCommonFilesTimeInfo = wps_remote_call($targetUrl, 'wps_compare_media_files', $encodeCommonFiles);

        $sourceCommonFilesTimeArray = json_decode($sourceCommonFilesTimeInfo, true);
        $targetCommonFilesTimeArray = json_decode($targetCommonFilesTimeInfo, true);
        // get list of common media files having more recent timestamp on source
        $commonFilesRecentTimestamp = array();
        if ($sourceCommonFilesTimeArray) {
            foreach ($sourceCommonFilesTimeArray as $key => $value) {

                if ($value['time'] >= $targetCommonFilesTimeArray[$key]['time']) {

                    $commonFilesRecentTimestamp[] = $value['path'];
                }
            }
        }
        // getting folder names inside media folders
        $folderNames = json_decode(wps_remote_call($sourceUrl, 'wps_scan_media_folder', ''), true);
        if ($folderNames) {
            $treeNewFilesArray = array();
            $treeCommonFilesArray = array();
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : Building media checkbox tree array.");
            // building tree array for new files
            foreach ($folderNames as $folder) {

                $mainFolderArray[$folder] = wps_data_compare_with_foldername($newFiles, $folder);

                $path = $folder . '/';
                $subFolders = json_decode(wps_remote_call($sourceUrl, 'wps_scan_media_folder', $path), true);

                if ($subFolders) {
                    foreach ($subFolders as $subfolder) {

                        $treeNewFilesArray[$folder][$subfolder] = wps_data_compare_with_foldername($mainFolderArray[$folder], $subfolder);
                    }
                }
            }

            // building tree array for new files
            foreach ($folderNames as $foldername) {
                $mainFolderArray[$foldername] = wps_data_compare_with_foldername($commonFilesRecentTimestamp, $foldername);
                $path = $foldername . '/';
                $subFolders = json_decode(wps_remote_call($sourceUrl, 'wps_scan_media_folder', $path), true);
                if ($subFolders) {
                    foreach ($subFolders as $subfolder) {

                        $treeCommonFilesArray[$foldername][$subfolder] = wps_data_compare_with_foldername($mainFolderArray[$foldername], $subfolder);
                    }
                }
            }

            $mediaContent = array('newFiles' => $treeNewFilesArray, 'commonRecentFiles' => $treeCommonFilesArray);
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : Returning media content.");
            return $mediaContent;
        }
        wps_log(WP_Sync_Admin::WPSYNCLOG . " : No media content found.");
        return false;
    } catch (SyncException $se) {
        // on getting exception show error page
        wps_error_page($se->getMessage());
        exit;
    }
    // for uncaught exception
    catch (Exception $e) {
        // on getting exception show error page
        wps_error_page($e->getMessage());
        exit;
    }
}

/**
 * Used for comparing and checking if media folder name present in links    
 * @since    1.0.0
 * @param  array  $needle  : Array of strings
 * @param  string $data    : Needle for exploding
 * @return array  $results  
 * 
 */
function wps_data_compare_with_foldername($data, $needle) {
    if ($data) {
        $result = array();
        foreach ($data as $key => $value) {

            $check = strpos($value, "/$needle/");
            if ($check !== false) {
                $result[] = $value;
            }
        }
        return $result;
    }
}

/**
 * Used for exploding string for any specifice position
 * @since    1.0.0
 * @param  array  $data    : Array of strings
 * @param  string $needle  : Needle for exploding
 * @return array  $results
 * 
 */
function wps_explode_data($data, $needle) {

    $result = array();
    if ($data) {
        foreach ($data as $string) {
            $explodedData = explode($needle, $string);
            $result[] = $explodedData['1'];
        }

        return $result;
    }
    return false;
}

/**
 * Used for making remote call using curl
 * @since    1.0.0
 * @param  string $url    : Remote site wpurl base
 * @param  string $action : Action to be called
 * @param  array  $params : POST parameters in Json format
 * @return string         : The returned content
 * 
 */
function wps_remote_call($url, $action, $params) {
    $remote = $url . '/wp-admin/admin-ajax.php?action=' . $action;
    $ch = curl_init($remote);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $params));
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // if HTTP connection not established, show error page and exit                        
    if ($code != 200) {

        $wpsInstance = new WP_Sync_Admin();
        $txtDomain = $wpsInstance->get_plugin_slug();
        throw new SyncException(__("Remote site not accessible (HTTP error). Please check your target instance url.", $txtDomain));
    }

    return $result;
}

/**
 * Used to Dump the database and save
 * @since  1.0.0
 * @param  string $synctag  : Synctag name
 * @return string $filename : Filename of zipped db backup
 */
function wps_makeBackup($synctag) {
    ob_start();
    wps_mysqldump();
    $sql = ob_get_clean();
    $tempdir = plugin_dir_path(dirname(__FILE__)) . '/admin/dbbackups';
    if (!file_exists($tempdir))
        mkdir($tempdir);
    $filename = $tempdir . '/db_' . $synctag . '.sql.gz';
    file_put_contents($filename, gzencode($sql));
    return $filename;
}

/**
 * Used to Dump the current MySQL table.
 * @since    1.0.0
 * @return  void
 */
function wps_mysqldump() {
    global $wpdb;
    $sql = "SHOW TABLES;";
    $result = mysql_query($sql);
    echo '/* Dump of database ' . DB_NAME . ' on ' . $_SERVER['HTTP_HOST'] . ' at ' . date('Y-m-d H:i:s') . " */\n\n";
    // Sidejump tables to skip 
    $tableName1 = $wpdb->prefix . "sync";
    $tableName2 = $wpdb->prefix . "sync_details";
    while ($row = mysql_fetch_row($result)) {
        // Skip for sidejump table
        if ($row[0] == $tableName1) {
            continue;
        }
        // Skip for sidejump_details table
        if ($row[0] == $tableName2) {
            continue;
        }
        echo wps_mysqldump_table_structure($row[0]);
        echo wps_mysqldump_table_data($row[0]);
    }
    mysql_free_result($result);
}

/**
 * Geting table structure
 * @since 1.0.0
 * @param string $table  Table name
 * @return string SQL
 */
function wps_mysqldump_table_structure($table) {
    echo "/* Table structure for table `$table` */\n\n";
    echo "DROP TABLE IF EXISTS `$table`;\n\n";
    $sql = "SHOW CREATE TABLE `$table`; ";
    $result = mysql_query($sql);
    if ($result) {
        if ($row = mysql_fetch_assoc($result)) {
            echo $row['Create Table'] . ";\n\n";
        }
    }
    mysql_free_result($result);
}

/**
 * Getting table data
 * @since 1.0.0
 * @param  string $table : Table name
 * @return string SQL
 */
function wps_mysqldump_table_data($table) {
    $sql = "SELECT * FROM `$table`;";
    $result = mysql_query($sql);

    echo '';
    if ($result) {
        $num_rows = mysql_num_rows($result);
        $num_fields = mysql_num_fields($result);
        if ($num_rows > 0) {
            echo "/* dumping data for table `$table` */\n";
            $field_type = array();
            $i = 0;
            while ($i < $num_fields) {
                $meta = mysql_fetch_field($result, $i);
                array_push($field_type, $meta->type);
                $i++;
            }
            $maxInsertSize = 100000;
            $index = 0;
            $statementSql = '';
            while ($row = mysql_fetch_row($result)) {
                if (!$statementSql)
                    $statementSql .= "INSERT INTO `$table` VALUES\n";
                $statementSql .= "(";
                for ($i = 0; $i < $num_fields; $i++) {
                    if (is_null($row[$i]))
                        $statementSql .= "null";
                    else {
                        switch ($field_type[$i]) {
                            case 'int':
                                $statementSql .= $row[$i];
                                break;
                            case 'string':
                            case 'blob' :
                            default:
                                $statementSql .= "'" . mysql_real_escape_string($row[$i]) . "'";
                        }
                    }
                    if ($i < $num_fields - 1)
                        $statementSql .= ",";
                }
                $statementSql .= ")";

                if (strlen($statementSql) > $maxInsertSize || $index == $num_rows - 1) {
                    echo $statementSql . ";\n";
                    $statementSql = '';
                } else {
                    $statementSql .= ",\n";
                }

                $index++;
            }
        }
    }
    mysql_free_result($result);
    echo "\n";
}

/**
 * Load a series of SQL statements.
 * @since 1.0.0
 * @param string $sql : SQL dump
 */
function wps_loadSql($sql) {
    $sql = preg_replace("|/\*.+\*/\n|", "", $sql);
    $queries = explode(";\n", $sql);
    foreach ($queries as $query) {
        if (!trim($query))
            continue;
        if (mysql_query($query) === false) {
            return false;
        }
    }

    return true;
}

/**
 * For storing option needs to be cache
 * @since 1.0.0
 * @return array : key-value pairs of selected current WordPress options
 */
function wps_cacheOptions() {
    //persist these options
    $defaultOptions = array('siteurl', 'home');
    $persistOptions = apply_filters('wps_persist_options', $defaultOptions);
    $optionCache = array();
    foreach ($persistOptions as $name) {
        $optionCache[$name] = get_option($name);
    }
    return $optionCache;
}

/**
 *  For storing superadmin user credentials having id 1 
 * @since 1.0.0
 * @return object
 */
function wps_cacheUserInfo() {

    global $wpdb;
    // Providing plugin table name
    $tableName = $wpdb->prefix . "users";

    $adminUserData = $wpdb->get_row(
            "SELECT *  FROM $tableName WHERE id = 1", ARRAY_A);
    if ($adminUserData) {
        return $adminUserData;
    }
    return false;
}

/**
 * For updating the cache options
 * @since 1.0.0 
 * @param array $optionCache : key-value pairs of options to restore
 */
function wps_restoreOptions($optionCache) {
    foreach ($optionCache as $name => $value) {
        update_option($name, $value);
    }
}

/**
 * For updating the admin user credentials
 * @since 1.0.0 
 * @param object $userInfo : Admin user info
 */
function wps_restoreAdminUserInfo($userInfo) {

    global $wpdb;

    // Providing plugin table name
    $tableName = $wpdb->prefix . "users";
    $wpdb->update($tableName, $userInfo, array('id' => 1));
}

/**
 * For checking synctag already exists or not 
 * @since 1.0.0 
 * @param $synctag : Sync process name
 * @return bool
 * 
 */
function wps_check_synctag_in_db($synctag) {
    global $wpdb;
    // Providing plugin table name
    $tableName = $wpdb->prefix . "sync_details";

    $syncExists = $wpdb->get_row(
            "SELECT id  FROM $tableName WHERE sync_tagname = '$synctag'", ARRAY_A);
    if ($syncExists) {
        return true;
    }
    return false;
}

/**
 * For getting backup files linked with requested synctags
 * @since 1.0.0 
 * @param $backupFileInfo : Synced files info
 * @return array
 * 
 */
function wps_get_backup_files($backupFileInfo) {

    try {
        global $wpdb;
        // Instantiate WP_Sync_Admin class and get text domain
        $wpsInstance = new WP_Sync_Admin();
        $txtDomain = $wpsInstance->get_plugin_slug();
        // Checking synced themes 
        if (!empty($backupFileInfo['0']->themes)) {
            $syncedThemes = maybe_unserialize($backupFileInfo['0']->themes);
            $themePath = WP_CONTENT_DIR . '/themes';
            // Removing backup themes
            $themesStatus = wps_remove_backup_files($themePath, $syncedThemes, $backupFileInfo['0']->sync_tagname);
            if (!$themesStatus) {
                throw new SyncException(__('Themes backup files clean up process failed.', $txtDomain));
            }
        }
        // Checking synced plugin
        if (!empty($backupFileInfo['0']->plugins)) {
            $syncedPlugins = maybe_unserialize($backupFileInfo['0']->plugins);
            $pluginPath = WP_CONTENT_DIR . '/plugins';
            // Removing backup plugins
            $pluginsStatus = wps_remove_backup_files($pluginPath, $syncedPlugins, $backupFileInfo['0']->sync_tagname);
            if (!$pluginsStatus) {
                throw new SyncException(__('Plugins backup files clean up process failed.', $txtDomain));
            }
        }

        // Checking db sync flag and remove db_backupfile
        if ($backupFileInfo['0']->db_sync_flag == 1) {
            $DbBackupPath = WP_PLUGIN_DIR . '/sidejump/admin/dbbackups/db_' . $backupFileInfo['0']->sync_tagname . '.sql.gz';

            if (file_exists($DbBackupPath)) {

                if (!@unlink($DbBackupPath)) {
                    throw new SyncException(__('Db backup files clean up process failed.', $txtDomain));
                }
            }
        }

        // Checking zip archive folder 
        $zipfilesBackupPath = WP_PLUGIN_DIR . '/sidejump/admin/zipfiles/' . $backupFileInfo['0']->sync_tagname.'/';
        if (file_exists($zipfilesBackupPath)) {
            $zipFiles = scandir($zipfilesBackupPath);
            foreach ($zipFiles as $oldfile) {
                // skipping ./.. from results
                if ($oldfile === '.' or $oldfile === '..')
                    continue;
                // deleting zip files folder
                  @unlink("$zipfilesBackupPath$oldfile");          
                
            }

            // Deleting zip folder created at the time of sync process
            if (!rmdir($zipfilesBackupPath)) {
                throw new SyncException(__('zipfiles folder deletion failed.', $txtDomain));
            }
        }

        // Removing synctag entry from database
        $tableName = $wpdb->prefix . "sync_details";
        $syncId = $backupFileInfo['0']->id;
        $delStatus = $wpdb->query("DELETE FROM $tableName WHERE id = $syncId ");
        if (!$delStatus) {
            return false;
        }

        return true;
    } catch (SyncException $se) {
        echo $se->getMessage();
        exit;
    }
}

/**
 * For removing backup files linked with requested synctags
 * @since 1.0.0 
 * 
 * 
 */
function wps_remove_backup_files($dirPath, $syncedFiles, $synctag) {
    foreach ($syncedFiles as $file) {
        $syncBackupFile = $dirPath . '/' . $file . '_' . $synctag . '.zip';

        if (file_exists($syncBackupFile)) {

            if (!@unlink($syncBackupFile)) {
                return false;
            }
            //unlink();
        }
    }
    return true;
}

/**
 * For displaying error page
 * @since 1.0.0 
 * 
 */
function wps_error_page($errmsg) {
    $errMessage = $errmsg;
    include_once(plugin_dir_path(dirname(__FILE__)) . '/admin/views/error-page.php');
}
