<?php

/**
 *
 * @package   Sidejump_Admin
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 * @link      http://sidejump.net
 * @copyright 2014 - Studio Hyperset, Inc.
 */
// Including common functions file
require_once(plugin_dir_path(dirname(__FILE__)) . '/includes/sync_exception.php');
require_once(plugin_dir_path(dirname(__FILE__)) . '/includes/functions.php');
require_once(plugin_dir_path(dirname(__FILE__)) . '/includes/ftp.php');

/**
 * This class is basically used for handling 
 * administrative side of the WordPress site.
 */
class WP_Sync_Admin {

    // Defining constant for WPSYNC plugin used in logging messages
    const WPSYNCLOG = 'Sidejump';

    // Instance of this class.
    protected static $instance = null;
    //Slug of the plugin screen.
    protected $plugin_screen_hook_suffix = null;
    // used for storing created zip files info
    protected static $createdZipInfo = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu. 
     *
     * @since     1.0.0
     */
    public function __construct() {

        // Call $plugin_slug from public plugin class.
        $plugin = WP_Sync::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'wps_enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'wps_enqueue_admin_scripts'));

       

        // Add the admin menu page and menu item.
        add_action('admin_menu', array($this, 'wps_add_plugin_admin_menu'));

        // Add submenu pages 
        add_action('admin_menu', array($this, 'wps_add_plugin_admin_submenu'));

        // Add ajax callbacks
        add_action('wp_ajax_sync_process', array($this, 'wps_trigger_sync_process'));
        add_action('wp_ajax_wps_zip_process', array($this, 'wps_zip_process'));
        add_action('wp_ajax_target_options', array($this, 'wps_get_target_options'));
        add_action('wp_ajax_wps_list_media_files', array($this, 'wps_list_media_files'));
        add_action('wp_ajax_wps_compare_media_files', array($this, 'wps_get_target_options'));
        add_action('wp_ajax_wps_scan_media_folder', array($this, 'wps_scan_media_folder'));
        add_action('wp_ajax_wps_list_folders', array($this, 'wps_list_folders'));
        add_action('wp_ajax_wps_save_created_zipinfo', array($this, 'wps_save_created_zipinfo'));
        add_action('wp_ajax_wps_mk_remote_dir', array($this, 'wps_mk_remote_dir'));
        add_action('wp_ajax_wps_start_extraction_process', array($this, 'wps_start_extraction_process'));
        add_action('wp_ajax_wps_is_writable', array($this, 'wps_is_writable'));
        add_action('wp_ajax_wps_db_push', array($this, 'wps_db_push'));
        add_action('wp_ajax_wps_db_pull', array($this, 'wps_db_pull'));
        add_action('wp_ajax_clean_process', array($this, 'wps_backup_clean_process'));


        // For non-priveleged ajax callbacks 
        add_action('wp_ajax_nopriv_wps_zip_process', array($this, 'wps_zip_process'));
        add_action('wp_ajax_nopriv_wps_list_media_files', array($this, 'wps_list_media_files'));
        add_action('wp_ajax_nopriv_wps_compare_media_files', array($this, 'wps_compare_media_files'));
        add_action('wp_ajax_nopriv_wps_scan_media_folder', array($this, 'wps_scan_media_folder'));
        add_action('wp_ajax_nopriv_wps_list_folders', array($this, 'wps_list_folders'));
        add_action('wp_ajax_nopriv_wps_save_created_zipinfo', array($this, 'wps_save_created_zipinfo'));
        add_action('wp_ajax_nopriv_wps_mk_remote_dir', array($this, 'wps_mk_remote_dir'));
        add_action('wp_ajax_nopriv_wps_start_extraction_process', array($this, 'wps_start_extraction_process'));
        add_action('wp_ajax_nopriv_wps_is_writable', array($this, 'wps_is_writable'));
        add_action('wp_ajax_nopriv_wps_db_push', array($this, 'wps_db_push'));
        add_action('wp_ajax_nopriv_wps_db_pull', array($this, 'wps_db_pull'));
    }

    /**
     * Return an instance of this class.
     * 
     * @return object : A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     1.0.0
     * @return    null    Return early if no settings page is registered.
     */
    public function wps_enqueue_admin_styles() {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        // Loading tab css only on Wp Sync detail page
        if ($screen->base == 'admin_page_wpsync-details') {
            wp_enqueue_style($this->plugin_slug . '-tab-styles', plugins_url('assets/css/SpryTabbedPanels.css', __FILE__), array(), WP_Sync::VERSION);
            wp_enqueue_style($this->plugin_slug . '-tree-custom-styles', plugins_url('assets/css/tree.custom.css', __FILE__), array(), WP_Sync::VERSION);
            wp_enqueue_style($this->plugin_slug . '-checkbox-tree-styles', plugins_url('assets/css/jquery.checkboxtree.css', __FILE__), array(), WP_Sync::VERSION);
        }
        wp_enqueue_style($this->plugin_slug . '-msgbox-styles', plugins_url('assets/css/alertify.core.css', __FILE__), array(), WP_Sync::VERSION);
        wp_enqueue_style($this->plugin_slug . '-msgbox2-styles', plugins_url('assets/css/alertify.default.css', __FILE__), array(), WP_Sync::VERSION);

        wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), WP_Sync::VERSION);
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since    1.0.0
     * @return    null    Return early if no settings page is registered.
     */
    public function wps_enqueue_admin_scripts() {

        if (!isset($this->plugin_screen_hook_suffix)) {

            return;
        }
        $screen = get_current_screen();
        // Loading tab js only on Wp Sync detail page
        if ($screen->base == 'admin_page_wpsync-details') {
            wp_enqueue_script($this->plugin_slug . '-tab-script', plugins_url('assets/js/SpryTabbedPanels.js', __FILE__), array('jquery'), WP_Sync::VERSION);
            wp_enqueue_script($this->plugin_slug . '-tree-custom-script', plugins_url('assets/js/tree.custom.min.js', __FILE__), array('jquery'), WP_Sync::VERSION);
            wp_enqueue_script($this->plugin_slug . '-checkboxtree-script', plugins_url('assets/js/jquery.checkboxtree.js', __FILE__), array('jquery'), WP_Sync::VERSION);
        }
        wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), WP_Sync::VERSION);
        wp_enqueue_script($this->plugin_slug . '-tooltip-script', plugins_url('assets/js/tooltip.js', __FILE__), array('jquery'), WP_Sync::VERSION);
        wp_enqueue_script($this->plugin_slug . '-msgbox-script', plugins_url('assets/js/alertify.min.js', __FILE__), array('jquery'), WP_Sync::VERSION);
    }

    

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     * @return   void
     */
    public function wps_add_plugin_admin_menu() {
        // Add a admin menu page for this plugin.
        $this->plugin_screen_hook_suffix = add_menu_page(
                '', __('Sidejump', $this->plugin_slug), 'manage_options', $this->plugin_slug, array($this, 'wps_display_plugin_admin_page'), WP_PLUGIN_URL . '/sidejump/admin/assets/images/icon.png'
        );
    }

    /**
     * Render the main admin/main page for this plugin.
     *
     * @since    1.0.0
     * @return   void
     */
    public function wps_display_plugin_admin_page() {
        include_once( 'views/admin.php' );
    }

    /**
     * Register the plugin submenus for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     * @return void
     */
    public function wps_add_plugin_admin_submenu() {
        // Add submenu pages for this plugin.
        $this->plugin_screen_hook_suffix = add_submenu_page(
                null
                , __('Sidejump', $this->plugin_slug)
                , __('Target Instance', $this->plugin_slug)
                , 'manage_options'
                , 'wpsync-details'
                , array($this, 'wps_content_details_page')
        );
        $this->plugin_screen_hook_suffix = add_submenu_page(
                $this->plugin_slug
                , __('Sidejump', $this->plugin_slug)
                , __('Sync Instances', $this->plugin_slug)
                , 'manage_options'
                , $this->plugin_slug
                , array($this, 'wps_display_plugin_admin_page')
        );
        $this->plugin_screen_hook_suffix = add_submenu_page(
                $this->plugin_slug
                , __('Sidejump', $this->plugin_slug)
                , __('Add a New WP Instance', $this->plugin_slug)
                , 'manage_options'
                , 'new-target-instance'
                , array($this, 'wps_new_target_instance')
        );
        $this->plugin_screen_hook_suffix = add_submenu_page(
                $this->plugin_slug
                , __('Sidejump', $this->plugin_slug)
                , __('All WP Instances', $this->plugin_slug)
                , 'manage_options'
                , 'wp-instances-listing'
                , array($this, 'wps_configured_instances')
        );
        $this->plugin_screen_hook_suffix = add_submenu_page(
                $this->plugin_slug
                , __('Sidejump', $this->plugin_slug)
                , __('Clean Up Backup Files', $this->plugin_slug)
                , 'manage_options'
                , 'cleanup'
                , array($this, 'wps_cleanup_page')
        );
        
        if (current_user_can( 'administrator' )) {
            global $submenu;
            $submenu['sidejump'][4] = array( __('Documentation &amp; Resources', $this->plugin_slug), 'manage_options' , 'http://sidejump.net/documentation-and-resources/', 'Sidejump' );
        }
        
		
        
    }

    /**
     * Render the add new target instance details page
     * 
     * @since  1.0.0
     */
    public function wps_new_target_instance() {
        include_once( 'views/new-target-instance.php' );
    }

    /**
     * Render the configured instances page
     * 
     * @since  1.0.0 
     */
    public function wps_configured_instances() {
        include_once( 'views/configured-instances.php' );
    }

    /**
     * Render the content details page
     * 
     * @since  1.0.0 
     */
    public function wps_content_details_page() {
        include_once( 'views/content-details.php' );
    }

    /**
     * Render the content details page
     * 
     * @since  1.0.0 
     */
    public function wps_cleanup_page() {
        include_once( 'views/clean-up.php' );
    }

    /**
     * Used for saving target instances to database
     *  
     * @since    1.0.0
     * @param    array  : Array having posted form data 
     * @return   boolean
     */
    public function wps_add_target_instance($data) {

        global $wpdb;
        // Providing plugin table name
        $tableName = $wpdb->prefix . "sync";
        // Message that will be returned 
        $message = 'success';
        // Getting FTP details
        $ftpDetails = array(
            'ftp_host' => $data['ftp_host'],
            'ftp_uname' => $data['ftp_uname'],
            'ftp_pswd' => $data['ftp_pswd'],
            'ftp_root_path' => $data['ftp_root_path']
        );
        // Serializing the FTP details 
        $ftpData = maybe_serialize($ftpDetails);
        // removing trailing slash if present
        $cleanedTargetUrl = rtrim($data['target_url'], "/");
        // Binding tables column names with values
        $dataBind = array(
            'target_name' => $data['target_name'],
            'target_url' => $cleanedTargetUrl,
            'target_ftp_details' => $ftpData
        );

        // If id exists, then update it else insert
        if ($data['id']) {
            $wpdb->update($tableName, $dataBind, array('id' => $data['id']));
            wps_log(self::WPSYNCLOG . ' : Target instance updated successfully. #gosidejump!');
        } else {
            // Checking new added target instance already exist or not
            if (!$this->wps_check_target_existence($tableName, $data['target_url'])) {

                if ($wpdb->insert($tableName, $dataBind)) {
                    // Depending upon the conditions, logging the respons
                    wps_log(self::WPSYNCLOG . ' : Target instance saved successfully. #gosidejump!');
                } else {
                    wps_log(self::WPSYNCLOG . ' : Target instance not saved.');
                    $message = 'failed';
                }
            } else {
                wps_log(self::WPSYNCLOG . ' : Target instance aleady exists.');
                $message = 'exists';
            }
        }

        return $message;
    }

    /**
     * Used for checking already existing target instance
     *  
     * @since    1.0.0
     * @param    string  $tableName : Tabel name
     * @param    string  $url       : Target instance url
     * @return   boolean
     */
    public function wps_check_target_existence($tableName = null, $url = null) {
        global $wpdb;
        // Fetching target instance
        $targetUrl = $wpdb->get_row(
                "SELECT target_url  FROM $tableName WHERE target_url = '$url'", OBJECT);
        // if already exist then return value 
        if ($targetUrl) {
            return $targetUrl;
        }

        return false;
    }

    /**
     * Used for removing target instance value
     *  
     * @since    1.0.0
     * @param    array  $data : Post array
     * @return   boolean
     */
    public function wps_remove_target_instance($data) {
        global $wpdb;

        // Providing plugin table name
        $tableName = $wpdb->prefix . "sync";
        // target instance id
        $tid = $data['target'];
        $delUrl = $wpdb->query("DELETE FROM $tableName WHERE id = $tid ");
        // if already exist then return value 
        if ($delUrl) {
            wps_log(self::WPSYNCLOG . ' : Target Instance deleted successfully. #gosidejump!');
            return true;
        }

        return false;
    }

    /**
     * Used for fetching source/target instances from database
     *  
     * @since    1.0.0
     * @param    int    : Target/Source Instance id
     * @return   object : Return all target instances present in db
     */
    public function wps_get_instances($id = null) {
        global $wpdb;

        // Providing plugin table name
        $tableName = $wpdb->prefix . "sync";
        // Set default value
        $condition = '';
        // Checking target instance id
        if ($id != null) {
            $condition = "WHERE id = $id";
        }
        $targetInstances = $wpdb->get_results(
                "SELECT * FROM $tableName $condition", OBJECT
        );

        // If data present than return it
        if ($targetInstances) {
            return $targetInstances;
        }

        return false;
    }

    /**
     * Used for getting target instance dropdown options
     *  
     * @since    1.0.0
     * @param    void
     * @return   void
     */
    public function wps_get_target_options() {
        global $wpdb;

        // Providing plugin table name
        $tableName = $wpdb->prefix . "sync";
        // local site url
        $localUrl = site_url();
        //getting id for local
        $localServerInfo = $wpdb->get_row(
                "SELECT id  FROM $tableName WHERE target_url = '$localUrl'", OBJECT);
        // source id from ajax call 
        $sid = $_POST['sid'];
        // applying condition for getting target options 
        $condition = "WHERE id = $localServerInfo->id";
        if ($localServerInfo->id == $sid) {
            $condition = "WHERE id != $sid";
        }
        $targetOptions = $wpdb->get_results(
                "SELECT * FROM $tableName $condition", OBJECT
        );
        // set default option
        $html = '';
        if ($targetOptions) {
            // creating html for target options
            foreach ($targetOptions as $option) {
                $optionName = __($option->target_name, $this->plugin_slug);
                $html .= "<option value='$option->id'>$optionName</option>";
            }
        } else {
            $html = "<option value='0'>" . __('No instance found.', $this->plugin_slug) . "</option>";
        }

        echo $html;
        die;
    }

    /**
     * Used for fetching list of folders for source
     *
     * @since    1.0.0
     * @param  string $dir     : Path of directory
     * @param  string $dirName : Directory name
     * @return array  $folders : Array of folder names for source instance
     */
    function wps_list_folders() {

        // get data using Curl request 
        $data = $_REQUEST['data'];
        $dirPath = WP_CONTENT_DIR . '/' . $data;
        $results = scandir($dirPath);
        // array for storing all folders available
        $folders = array();
        foreach ($results as $result) {

            // skipping ./.. from results
            if ($result === '.' or $result === '..')
                continue;

            if (is_dir($dirPath . '/' . $result)) {
                $folders[] = $result;
            }
        }

        $output = json_encode($folders);
        echo $output;
        die;
    }

    /**
     * Provide media files path and timestamp
     * that will be used for comparison
     * @since    1.0.0
     * @return   array   $result : Array of media files
     */
    public function wps_compare_media_files() {
        // get data using Curl request 
        $data = $_REQUEST['data'];
        // strip extra slashes 
        $cleanData = stripslashes($data);
        //decoding in orginal form
        $decodedData = json_decode($cleanData);
        $result = array();
        if ($decodedData) {
            $i = 0;
            foreach ($decodedData as $path) {
                $result[$i]['path'] = $path;
                $result[$i]['time'] = filemtime(WP_CONTENT_DIR . '/' . $path);
                $i++;
            }
            // encoding data for sending back as curl response
            $finalResult = json_encode($result);
        }

        echo $finalResult;
        die;
    }

    /**
     * Getting media foldernames for source instance
     * 
     * @since    1.0.0
     * @return   array  $finalResult : Array of media folders
     */
    public function wps_scan_media_folder() {

        // get data using Curl request 
        $data = $_REQUEST['data'];
        $pathsegemt = '';
        if ($data) {
            $pathsegemt = $data;
        }
        $path = WP_CONTENT_DIR . '/uploads/' . $pathsegemt;
        $root = scandir($path);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_dir("$path$value")) {
                $result[] = $value;
            }
        }

        $finalResult = json_encode($result);
        echo $finalResult;
        die;
    }

    /**
     * Used for listing media files inside uploads directories 
     *  
     * @since    1.0.0
     * @return   void
     */
    public function wps_list_media_files() {

        // specify media uploads folder path
        $dir = WP_CONTENT_DIR . '/uploads/';

        if (!is_dir($dir)) {
            throw new SyncException(__('Uploads folder does not exists.', $this->plugin_slug));
        }

        $result = array();
        // scanning uploads directory
        $root = scandir($dir);

        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir$value")) {
                $result[] = "$dir$value";
                continue;
            }
            if (is_dir("$dir$value")) {
                $result[] = "$dir$value/";
            }
            foreach (self::list_directory("$dir$value/") as $value) {

                $result[] = $value;
            }
        }
        $output = json_encode($result);
        echo $output;
        die;
    }

    /**
     * Used for triggering sync process on getting ajax request
     * and show the associated message 
     * 
     * @since    1.0.0
     * @param    void
     * @return   void
     */
    public function wps_trigger_sync_process() {

        try {
            //increasing script execution time limit 
            set_time_limit(120);

            // getting source and target info
            $sourceDetails = $this->wps_get_instances($_POST['sid']);
            $sourceUrl = $sourceDetails[0]->target_url;
            $targetDetails = $this->wps_get_instances($_POST['tid']);
            $targetUrl = $targetDetails[0]->target_url;
            // Data to be synced
            $syncData = array('sourceinfo' => $sourceDetails, 'targetinfo' => $targetDetails, 'syncform' => $_POST);

            // generating sync tag name that will used a syncprocess name
            $syncTagName = 'sync_' . time();
            // Data to be sent for zip process
            $finalData = array('syncData' => $syncData, 'synctag' => $syncTagName);
            // encoding Ajax post data
            $encodefinalData = json_encode($finalData);
            // logging state
            wps_log(self::WPSYNCLOG . " : Checking wp-content directory writable permissions. ");
            // checking wp-content dir writable permission on target server
            $isWritable = wps_remote_call($targetUrl, 'wps_is_writable', null);

            // If response is OK then start zip process
            if ($isWritable == 'OK') {
                // initailizing final response variable
                $finalResponse = 0;
                // logging state
                wps_log(self::WPSYNCLOG . " : Making curl request for wps_zip_process ");
                // Checking for items that need zip creation process
                if ((isset($syncData['syncform']['wps_theme'])) || (isset($syncData['syncform']['wps_plugin'])) || (isset($syncData['syncform']['wps_new_media'])) || (isset($syncData['syncform']['wps_common_media']))) {

                    // get zip process response using curl
                    $finalResponse = wps_remote_call($sourceUrl, 'wps_zip_process', $encodefinalData);

                    if ($finalResponse != 'DONE') {
                        throw new SyncException(__($finalResponse, $this->plugin_slug));
                    }
                }

                // if database synchronization is also requested
                if ($syncData['syncform']['wps_db_value'] == 1) {

                    if ($syncData['syncform']['wps_sync_type'] == 'pull') {

                        $sql = wps_remote_call($syncData['sourceinfo']['0']->target_url, 'wps_db_pull', null);
                        if ($sql && preg_match('|^/\* Dump of database |', $sql)) {

                            //backup current database with synctagname
                            $backupfile = wps_makeBackup($syncTagName);

                            //store some options to restore after sync
                            $optionCache = wps_cacheOptions();
                            // store admin user info 
                            $adminUserInfo = wps_cacheUserInfo();
                            //load the new data
                            if (wps_loadSql($sql)) {
                                //clear object cache
                                wp_cache_flush();

                                //restore options
                                wps_restoreOptions($optionCache);
                                //reinstate admin credentials
                                wps_restoreAdminUserInfo($adminUserInfo);

                                // Making entry in db for database sync
                                $this->wps_save_db_sync_info($syncTagName, $syncData);


                                $finalResponse = 'DONE';
                            } else {
                                throw new SyncException(__('Database Sync during PULL process failed. MySQL error.', $this->plugin_slug));
                            }
                        } else {
                            throw new SyncException(__('Database Sync PULL process failed. Invalid dump.', $this->plugin_slug));
                        }
                    } else {
                        ob_start();
                        wps_mysqldump();
                        $sql = ob_get_clean();
                        $sqlInfo = array('synctag' => $syncTagName, 'sql' => $sql);
                        $encodeSqlInfo = json_encode($sqlInfo);
                        $DbPushResponse = wps_remote_call($syncData['targetinfo']['0']->target_url, 'wps_db_push', $encodeSqlInfo);

                        if ($DbPushResponse != 'OK') {
                            throw new SyncException(__('Database Sync during PUSH process failed. MySQL error.', $this->plugin_slug));
                        }
                        // Making entry in db for database sync
                        $this->wps_save_db_sync_info($syncTagName, $syncData);
                        $finalResponse = 'DONE';
                    }
                }

                if ($finalResponse == 'DONE') {
                    // display response
                    _e('Sync process completed successfully. #gosidejump!', $this->plugin_slug);
                    exit;
                } else {
                    _e('Sync process failed. Please try again.', $this->plugin_slug);
                    exit;
                }
            } elseif ($isWritable == '-1') {
                throw new SyncException(__('wp-content folder is not writable on target server. Please ensure the following directories have write permissions (CHMOD 777): wp-content/plugins, wp-content/themes, and wp-content/uploads. Depending on your server settings, you may also need to update the Sidejump backup folder\'s permissions: wp-content/plugins/sidejump/admin/dbbackups.', $this->plugin_slug));
            } else {
                throw new SyncException(__('Have you activated the plugin on the target server?', $this->plugin_slug));
            }
        } catch (SyncException $se) {
            // displaying  SyncException
            echo $se->getMessage();
            exit;
        } catch (Exception $e) {
            // displaying unhandled Exception
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Used for creating zip and database migration during sync process
     *  
     * @since    1.0.0
     * @return   boolean
     */
    public function wps_zip_process() {

        try {

            // getting data from curl request and strip the slashes
            $cleanData = stripslashes($_REQUEST['data']);
            // decoding data in original form
            $data = json_decode($cleanData, true);
            // making directory at source site for storing zip files with 0777 permissions
            $oldumask = umask(0);
            $syncTagName = $data['synctag'];
            $zipFolderPath = plugin_dir_path(__FILE__) . '/zipfiles/' . $syncTagName;
            
            if (!mkdir($zipFolderPath, 0777)) {
                wps_log(self::WPSYNCLOG . " : Could not create folder for storing zip files.");
                throw new SyncException(__('Could not create folder for storing zip files.', $this->plugin_slug));
            }
            umask($oldumask);
            // if themes zip creation is requested
            if (isset($data['syncData']['syncform']['wps_theme'])) {
                $themeResponse = $this->wps_start_zip_creation($data['syncData']['syncform']['wps_theme'], 'themes', $zipFolderPath);
                if ($themeResponse === true) {
                    wps_log(self::WPSYNCLOG . " : Source instance themes zip created successfully. #gosidejump!");
                } else {
                    wps_log(self::WPSYNCLOG . " : Source instance themes zip creation failed.");
                    throw new SyncException(__('Source instance themes zip creation failed.', $this->plugin_slug));
                }
            }
            // if plugins zip creation is requested
            if (isset($data['syncData']['syncform']['wps_plugin'])) {
                $pluginResponse = $this->wps_start_zip_creation($data['syncData']['syncform']['wps_plugin'], 'plugins', $zipFolderPath);
                if ($pluginResponse === true) {
                    wps_log(self::WPSYNCLOG . " : Source instance plugins zip created successfully. #gosidejump!");
                } else {
                    wps_log(self::WPSYNCLOG . " : Source instance plugins zip creation failed.");
                    throw new SyncException(__('Source instance plugins zip creation failed.', $this->plugin_slug));
                }
            }

            // if media newly added files zip creation is requested
            if (isset($data['syncData']['syncform']['wps_new_media'])) {
                $newMediaResponse = $this->wps_zip_create_for_media('new_media_files', $data['syncData']['syncform']['wps_new_media'], $zipFolderPath);
                if ($newMediaResponse === true) {
                    wps_log(self::WPSYNCLOG . " : Source instance newly added media files zip created successfully. #gosidejump!");
                } else {
                    wps_log(self::WPSYNCLOG . " : Source instance newly added media files zip creation failed.");
                    throw new SyncException(__('Source instance newly added media files zip creation failed.', $this->plugin_slug));
                }
            }
            // if media common recent files zip creation is requested
            if (isset($data['syncData']['syncform']['wps_common_media'])) {
                $commonMediaResponse = $this->wps_zip_create_for_media('common_media_files', $data['syncData']['syncform']['wps_common_media'], $zipFolderPath);
                if ($commonMediaResponse === true) {
                    wps_log(self::WPSYNCLOG . " : Source instance common but recent media files zip created successfully. #gosidejump!");
                } else {
                    wps_log(self::WPSYNCLOG . " : Source instance common but recent media files zip creation failed.");
                    throw new SyncException(__('Source instance common but recent media files zip creation failed.', $this->plugin_slug));
                }
            }


            if (!empty($this->createdZipInfo)) {
                $dbFlag = $data['syncData']['syncform']['wps_db_value'];
                $zippedFilesArray = array('synctag' => $syncTagName, 'dbFlag' => $dbFlag, 'zipFileNames' => $this->createdZipInfo);
                $encodedzippedFilesArray = json_encode($zippedFilesArray);
                wps_log(self::WPSYNCLOG . " : Saving zipped files info at source and target databases ");
                // Saving archived files info in source instance db
                $sourceDbResponse = wps_remote_call($data['syncData']['sourceinfo']['0']['target_url'], 'wps_save_created_zipinfo', $encodedzippedFilesArray);
                // Saving archived files info in target instance db
                $targetDbResponse = wps_remote_call($data['syncData']['targetinfo']['0']['target_url'], 'wps_save_created_zipinfo', $encodedzippedFilesArray);
                // Making sync dir on target server
                $targetMkDirResponse = wps_remote_call($data['syncData']['targetinfo']['0']['target_url'], 'wps_mk_remote_dir', $syncTagName);

                // uploading files on target server
                $status = wps_transfer_files($data['syncData']['targetinfo'], $syncTagName);
                // if files uploaded then starts extraction process
                if ($status == true) {
                    $targetExtractFilesResponse = wps_remote_call($data['syncData']['targetinfo']['0']['target_url'], 'wps_start_extraction_process', $syncTagName);
                    echo $targetExtractFilesResponse;
                    exit;
                }
            }
        } catch (SyncException $se) {
            // displaying  SyncException
            echo $se->getMessage();
            exit;
        } catch (Exception $e) {
            // displaying uncaught Exception
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Used for starting zip of the requested directories
     *  
     * @since    1.0.0
     * @param    array   $data          : Selected themes,plugins
     * @param    string  $dir           : Source directory name
     * @param    string  $zipFolderPath : Zip files folder path
     * @return   boolean
     */
    public function wps_start_zip_creation($data, $dir, $zipFolderPath) {
        if ($data) {
            foreach ($data as $itemName) {
                // logging zip process
                wps_log(self::WPSYNCLOG . " : Source instance theme ($itemName) zip creation started ");
                //get response
                $response = $this->wps_zip_create($itemName, $dir, $zipFolderPath);

                if ($response !== true) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Used for creating zip of the requested directories
     *      
     * @since    1.0.0
     * @param    string  $zipName : Name of zip to be created
     * @param    string  $dir     : Type of content (themes or plugins)
     * @return   boolean
     */
    public function wps_zip_create($zipName, $dir, $zipFolderPath) {

        // Path where zip file will be created
        $zip_file = $zipFolderPath . '/' . $zipName . '.zip';
        // source directory path 
        $sourceDir = WP_CONTENT_DIR . '/' . $dir . '/' . $zipName . '/';
        // Getting file list from source dir provided
        $file_list = $this->list_directory($sourceDir);

        // Loading PHP ZIP Archive Class
        $zip = new ZipArchive();
        //Creating zip
        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {

            foreach ($file_list as $file) {
                if ($file !== $zip_file) {

                    $zip->addFile($file, substr($file, strlen($sourceDir)));
                }
            }
            // storing created media zips info
            $this->createdZipInfo[$dir][] = $zipName;
            $zip->close();

            return true;
        }

        return false;
    }

    /**
     * Used for listing files inside directories
     *  
     * @since    1.0.0
     * @param    string  $dir    : File/Dir path location
     * @return   array   $result : Array of files
     */
    public function list_directory($dir) {
        $result = array();
        $root = scandir($dir);

        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir$value")) {
                $result[] = "$dir$value";
                continue;
            }
            if (is_dir("$dir$value")) {
                $result[] = "$dir$value/";
            }
            foreach (self::list_directory("$dir$value/") as $value) {

                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Used for creating zip of the media uploads
     *  
     * @since    1.0.0
     * @param    string  $dir           : Zip file name
     * @param    array   $filelist      : List of files need to be archived
     * @param    string  $zipFolderPath : Zip destination dir path
     * @return   boolean
     */
    public function wps_zip_create_for_media($dir, $filelist, $zipFolderPath) {

        // Path where zip file will be created
        $zip_file = $zipFolderPath . '/' . $dir . '.zip';
        // Getting file list from source dir provided
        $file_list = $filelist;

        // Loading PHP ZIP Archive Class
        $zip = new ZipArchive();
        //Creating zip
        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {

            foreach ($file_list as $file) {
                // adding root content dir path
                $filepath = WP_CONTENT_DIR . '/' . $file;

                if (is_file($filepath) && $filepath !== $zip_file) {

                    $zip->addFile($filepath, substr($filepath, strlen(WP_CONTENT_DIR . '/uploads/')));
                }
            }
            $zip->close();
            if (file_exists($zip_file)) {
                // storing created media zips info
                $this->createdZipInfo['media'][] = $dir;
            }

            return true;
        }
        return false;
    }

    /**
     * Used for saving created zip files name
     * @since    1.0.0
     * @param  $data    array  : Array of zip filenames
     * @param  $tagName string : Sync Tag name
     * @return $results array 
     * 
     */
    public function wps_save_created_zipinfo() {

        global $wpdb;
        // get data using Curl request 
        $data = $_REQUEST['data'];
        // strip extra slashes 
        $cleanData = stripslashes($data);
        //decoding in orginal form
        $decodedData = json_decode($cleanData, true);

        $tableName = $wpdb->prefix . "sync_details";
        if ($decodedData) {

            $syncDetails = array(
                'sync_tagname' => $decodedData['synctag'],
                'db_sync_flag' => $decodedData['dbFlag']
            );
            if ($decodedData['zipFileNames']) {
                foreach ($decodedData['zipFileNames'] as $key => $value) {
                    $syncDetails[$key] = maybe_serialize($value);
                }
            }

            if (!$wpdb->insert($tableName, $syncDetails)) {
                //if insertion failed throwing exception
                throw new SyncException(__('Zip files tracking information insertion failed.', $this->plugin_slug));
            }

            return true;
        }

        return false;
    }

    /**
     * Used for making directory at target site 
     * for storing zip files
     * @since    1.0.0
     * @return   bool
     * 
     */
    public function wps_mk_remote_dir() {

        $syncTagName = $_REQUEST['data'];
        // making directory at target site for storing zip files with 0777 permissions
        $oldumask_t = umask(0);
        $zipFolderPath = plugin_dir_path(__FILE__) . '/zipfiles/' . $syncTagName;
        if (!mkdir($zipFolderPath, 0777)) {
            wps_log(self::WPSYNCLOG . " : Couldn\'t create folder at target site for storing zip files.");
            throw new SyncException(__('Could not create folder at target site for storing zip files.', $this->plugin_slug));
        }
        umask($oldumask_t);

        return true;
    }

    /**
     * Used for checking wp-content dir writable or not
     * 
     * @since    1.0.0
     * @return   bool
     * 
     */
    public function wps_is_writable() {
        // wp-content dir path
        $contentDir = WP_CONTENT_DIR;
        if (is_writable($contentDir)) {
            echo 'OK';
            exit;
        }
        echo '-1';
        exit;
    }

    /**
     * Used for renaming and extracting zip files to their desired directory at target site 
     * 
     * @since    1.0.0
     * @return   bool
     * 
     */
    public function wps_start_extraction_process() {

        try {
            // get data using Curl request 
            $data = $_REQUEST['data'];
            // fetch tracked info from database
            $trackedInfo = $this->wps_get_sync_tracked_info($data);

            $synctag = $trackedInfo['0']->sync_tagname;
            if ($trackedInfo['0']->themes) {
                $themes = maybe_unserialize($trackedInfo['0']->themes);
                // creating archive of old themes if present
                $zipThemes = $this->wps_zip_old_dir('themes', $themes, $synctag);
                // extracting archive of themes migrated from source instance
                $extractThemes = $this->wps_extract_files('themes', $themes, $synctag);
            }
            if ($trackedInfo['0']->plugins) {
                $plugins = maybe_unserialize($trackedInfo['0']->plugins);
                // creating archive of old plugins if present
                $zipPlugins = $this->wps_zip_old_dir('plugins', $plugins, $synctag);
                // extracting archive of plugins migrated from source instance
                $extractPlugins = $this->wps_extract_files('plugins', $plugins, $synctag);
            }
            if ($trackedInfo['0']->media) {
                $media = maybe_unserialize($trackedInfo['0']->media);
                $extractMedia = $this->wps_extract_files('uploads', $media, $synctag);
            }

            echo 'DONE';
            exit;
        } catch (SyncException $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

    /**
     * Used for making archive as a backup for old directories 
     * 
     * @since    1.0.0
     * @param    string $type   : Content type (themes or plugins)
     * @param    array  $data   : Array of foldernames
     * @param    string $synctag: Sync tag name
     * @return   bool
     * 
     */
    public function wps_zip_old_dir($type, $data, $synctag) {
        try {
            if ($data) {

                foreach ($data as $folderName) {
                    // specify original source dir on target server that need to be archived 
                    $sourceDir = WP_CONTENT_DIR . '/' . $type . '/' . $folderName . '/';
                    $zip_file = WP_CONTENT_DIR . '/' . $type . '/' . $folderName . '_' . $synctag . '.zip';

                    //checking if directory exists or not           
                    if (is_dir($sourceDir)) {
                        $file_list = $this->list_directory($sourceDir);
                        // Loading PHP ZIP Archive Class
                        $zip = new ZipArchive();
                        //Creating zip
                        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {

                            foreach ($file_list as $file) {
                                if ($file !== $zip_file) {

                                    $zip->addFile($file, substr($file, strlen($sourceDir)));
                                }
                            }

                            $zip->close();
                        } else {
                            throw new SyncException(__("Zip creation process of old item ($folderName) failed on target server.", $this->plugin_slug));
                        }
                    }
                }
                return true;
            }
            return false;
        } catch (SyncException $se) {
            echo $se->getMessage();
            exit;
        }
    }

    /**
     * Used for extracting requested files 
     * 
     * @since    1.0.0
     * @param    string $type   : Content type (themes or plugins)
     * @param    array  $data   : Array of foldernames
     * @param    string $synctag: Sync tag name
     * @return   bool
     * 
     */
    public function wps_extract_files($type, $data, $synctag) {
        try {
            if ($data) {
                // creating ZIP Archive instance
                $zip = new ZipArchive;
                foreach ($data as $filename) {
                    $zipFilePath = WP_PLUGIN_DIR . '/sidejump/admin/zipfiles/' . $synctag . '/' . $filename . '.zip';
                    if ($type == 'uploads') {
                        $extractLocation = WP_CONTENT_DIR . '/' . $type . '/';
                    } else {
                        $extractLocation = WP_CONTENT_DIR . '/' . $type . '/' . $filename . '/';
                    }

                    // extracting zip to desired location
                    if ($zip->open($zipFilePath) === TRUE) {
                        if(!$zip->extractTo($extractLocation)){
                            throw new SyncException(__("$filename.zip extraction failed.Please check write permissions on '$filename' folder.", $this->plugin_slug)); 
                        }
                        $zip->close();
                    } else {

                        throw new SyncException(__("$filename.zip extraction failed.Please check write permissions on $type folder.", $this->plugin_slug));
                    }
                }
                return true;
            }

            return false;
        } catch (SyncException $se) {
            echo $se->getMessage();
            exit;
        }
        
    }

    /**
     * Used for fetching tracked info based on synctag 
     * 
     * @since    1.0.0
     * @param    string $synctag : Synctag Name
     * @return   object
     * 
     */
    public function wps_get_sync_tracked_info($synctag = null) {
        global $wpdb;
        $tableName = $wpdb->prefix . "sync_details";
        $condition = '';
        if ($synctag != null) {
            $condition = "WHERE sync_tagname = '$synctag'";
        }
        $query = "SELECT * from $tableName $condition";
        $result = $wpdb->get_results($query, OBJECT);
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Used for storing syncinfo for database (Only for case inwhich only db sync is requested)
     * 
     * @since    1.0.0
     * @param    $synctag  : Sync process name
     * @param    $syncData : Sync data (db sync requested or not)
     * @return   bool
     * 
     */
    public function wps_save_db_sync_info($synctag, $syncData) {
        $synctagExists = wps_check_synctag_in_db($synctag);
        if (!$synctagExists) {
            $dbFlag = $syncData['syncform']['wps_db_value'];
            $syncDbInfo = array('synctag' => $synctag, 'dbFlag' => $dbFlag);
            $encodedzippedFilesArray = json_encode($syncDbInfo);
            wps_log(self::WPSYNCLOG . " : Saving zipped files info at source and target databases ");
            // Saving archived files info in source instance db
            $sourceDbResponse = wps_remote_call($syncData['sourceinfo']['0']->target_url, 'wps_save_created_zipinfo', $encodedzippedFilesArray);
            // Saving archived files info in target instance db
            $targetDbResponse = wps_remote_call($syncData['targetinfo']['0']->target_url, 'wps_save_created_zipinfo', $encodedzippedFilesArray);
        }
    }

    /**
     * Used for pushing database on target server
     * 
     * @since    1.0.0
     * @return   object
     * 
     */
    public function wps_db_push() {

        // get data using Curl request 
        $data = $_REQUEST['data'];
        // strip extra slashes 
        $cleanData = stripslashes($data);
        //decoding in orginal form
        $decodedData = json_decode($cleanData, true);

        // Getting sql to be pushed on target server.
        $sql = $decodedData['sql'];
        if ($sql && preg_match('|^/\* Dump of database |', $sql)) {

            //backup current DB
            wps_makeBackup($decodedData['synctag']);

            //store options
            $optionCache = wps_cacheOptions();

            // store admin user info 
            $adminUserInfo = wps_cacheUserInfo();
            //load posted data
            wps_loadSql($sql);

            //clear object cache
            wp_cache_flush();

            //reinstate options
            wps_restoreOptions($optionCache);
            //reinstate admin credentials
            wps_restoreAdminUserInfo($adminUserInfo);

            echo 'OK';
            exit;
        } else {
            echo '-1';
            exit;
        }
    }

    /**
     * Used for pulling database on source server
     * 
     * @since    1.0.0 
     */
    public function wps_db_pull() {
        //dump DB and GZip it
        header('Content-type: application/octet-stream');
        wps_mysqldump();
        exit;
    }

    /**
     * Used for removing backup files
     * 
     * @since    1.0.0
     * 
     */
    public function wps_backup_clean_process() {
        try {
            // getting requested sync tags
            $data = $_POST['wps_sync_tags'];
            if ($data) {
                foreach ($data as $synctag) {
                    $backupFileInfo = $this->wps_get_sync_tracked_info($synctag);
                    $status = wps_get_backup_files($backupFileInfo);
                    if (!$status) {
                        throw new SyncException(__("CleanUp process failed"));
                    }
                }
                
                _e('Clean up completed successfully. #gosidejump!',$this->plugin_slug);
                exit;
            }
        } catch (SyncException $se) {
            echo $se->getMessage();
            exit;
        }
    }

}
