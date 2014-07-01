<?php

/**
 * File used for FTP functions.
 *
 *
 * @package Sidejump
 * @author  Jenis Patel <jenis.patel@daffodilsw.com>
 */

/**
 * Used for loggin and transfering zip files on target ftp server
 *
 * @since    1.0.0
 * @param  array  $targetInfo  : Target Ftp details
 * @param  string $syncTagName : Sync tag name
 * @return bool  
 */
function wps_transfer_files($targetInfo, $syncTagName) {
    
    $wpsInstance = new WP_Sync_Admin();
    $txtDomain = $wpsInstance->get_plugin_slug();
    // getting ftp info
    $serialisedFtpInfo = $targetInfo['0']['target_ftp_details'];
    $ftpInfo = maybe_unserialize($serialisedFtpInfo);
    $hostName = $ftpInfo['ftp_host'];
    $remoteDirPath = $ftpInfo['ftp_root_path'] . '/wp-content/plugins/sidejump/admin/zipfiles/' . $syncTagName;
    // set up basic connection
    $connId = @ftp_connect($hostName);
    // checking connection established or not
    if (@ftp_login($connId, $ftpInfo['ftp_uname'], $ftpInfo['ftp_pswd'])) {
        // logging FTP connection status
        wps_log(WP_Sync_Admin::WPSYNCLOG . " : FTP connection established for $hostName");
        $wpsInstance = new WP_Sync_Admin();
        $zipFolderPath = WP_PLUGIN_DIR . '/sidejump/admin/zipfiles/' . $syncTagName . '/';

        $zipfiles = $wpsInstance->list_directory($zipFolderPath);
        if ($zipfiles) {
            
            foreach ($zipfiles as $file) {
                $path_parts = pathinfo($file);
                $remoteFile = $remoteDirPath . '/' . $path_parts['basename'];
                
                $transferStatus = wps_ftp_upload($connId, $remoteFile, $file, FTP_BINARY);
                if($transferStatus === false){
                    wps_log(WP_Sync_Admin::WPSYNCLOG . " : File ($file) not uploaded on target server.");
                    throw new SyncException (__("File ($file) transfer failed.Please check your target's remote dir path.",$txtDomain));
                }
              
            }
            // logging info
            wps_log(WP_Sync_Admin::WPSYNCLOG . " : Zip files transferred successfully.");
            return true;
        }
       
        
    } else {
         // logging FTP status and throwing exception
        wps_log(WP_Sync_Admin::WPSYNCLOG . " : FTP connection failed for $hostName");
        throw new SyncException (__("FTP connection could not be established. Please check your target instance ftp details.",$txtDomain));
    }
}

/**
 * Used for uploading file using ftp
 *
 * @since    1.0.0
 * @param  resource  $connId  : Specifies the FTP connection to use
 * @param  string $remoteFile : Specifies the file path to upload to
 * @param  string $files      : Specifies the path of the file to upload
 * @param  string $mode       : Specifies the transfer mode. (FTP_ASCII or FTP_BINARY)
 * @return bool
 */
function wps_ftp_upload($connId,$remoteFile,$file,$mode) {
   
    // uploading files
    $status = @ftp_put($connId, $remoteFile, $file, $mode);
    //changing permissions
    @ftp_chmod($connId, 0777, $remoteFile);
    return $status;
}
