<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Sidejump
 * @author    Jenis Patel <jenis.patel@daffodilsw.com>
 * @license   GPLv3 or later
 * @link      http://sidejump.net
 * @copyright 2014 - Studio Hyperset, Inc.
 */
// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
// Deleting plugin tables on unistallation

global $wpdb;
$tableMain = $wpdb->prefix . "sync";
$tableDetails = $wpdb->prefix . "sync_details";

$wpdb->query("DROP TABLE IF EXISTS $tableMain");
$wpdb->query("DROP TABLE IF EXISTS $tableDetails");

