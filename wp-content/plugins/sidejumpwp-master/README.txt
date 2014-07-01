=== Sidejump ===
Contributors: studiohyperset
Donate link: http://sidejump.net
Tags: admin, administration, cms, media, multisite, network, plugin, simple, wpmu, sync, synchronize
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.0
License: GPLv3 or later
License URI: http://sidejump.net/terms/

Sidejump helps synchronize development, staging, and production instances of WordPress.

== Description ==

WordPress developers and site managers routinely maintain two or three versions of the same site (eg., development, staging, production). Keeping these instances in sync can be a challenge, and manually updating settings, theme and media files, plugins, and content is inexact, error-prone, and time-consuming.

Using Sidejump, you can synchronize WordPress databases, content, files, and settings with just one click.

== Installation ==

1. Install and activate Sidejump on every WordPress instance you want to manage. (If you're unfamiliar with installing WordPress plugins, please read [this page from the Codex](http://codex.wordpress.org/Managing_Plugins)). This will create a new top-level admin menu titled "Sidejump."

2. Enter connection details for each site you want to manage via "Sidejump" > "Add New WP Instance."

3. Before syncing, ensure the following directories have write permissions (CHMOD 777) on each instance: wp-content/plugins, wp-content/themes, and wp-content/uploads. (Depending on your server settings, you may also need to update the Sidejump backup folder's permissions: wp-content/plugins/sidejump/admin/dbbackups.)

4. After adding WP instances, you can add, delete, and manage them via "Sidejump" > "All WP Instances."

5. In case of errors and unintended consequences, Sidejump will back up the target instance before synchronization. Theme, plugin, and media files are stored in their respective directories (as zipped files), and database backup files are stored as gzipped SQL files here: wp-content/plugins/sidejump/admin/dbbackups/. Users can delete old backup files using the "Clean Up Backup Files" feature ("Sidejump" > "Clean Up Backup Files"). For management and tracking purposes, Sidejump names all backup files "sync_%UNIX-TIMESTAMP%".

6. When you're ready to synchronize your WP instances, select "Sidejump" > "Sync Instances." For best results, always synchronize wp-content files (themes, media, plugins) before synchronizing the database.

7. For additional documentation and resources, please visit [http://sidejump.net/documentation-and-resources](http://sidejump.net/documentation-and-resources/).

== Frequently Asked Questions ==

= Does Sidejump synchronize WP core files?  =
No. To avoid potentially catastrophic file/database corruption, Sidejump does not sync WP core files.

= What sort of synchronization details should I be aware of? =

Since most plugins are database-dependent, when you synchronize plugin files, you will also synchronize the database.

Sidejump will overwrite the contents of the target database with the contents of the source database. The only exceptions follow: the site and home URL options, Sidejump tables, and primary admin user (assumed to be ID #1) are never overwritten.

= Who can use Sidejump?  =
Sidejump is only available to admin-level users.

= By using Sidejump, what terms do I agree to? =

Please use Sidejump with caution. Users accept all liability for use according to these terms [http://sidejump.net/terms/](http://sidejump.net/terms/). 

= Where can I review Sidejump documentation and resources? = 

For Sidejump documentation and resources, please visit [http://sidejump.net/documentation-and-resources](http://sidejump.net/documentation-and-resources/).

== Screenshots ==

Try a fully-functioning demo here: [http://demo.sidejump.net](http://demo.sidejump.net/wp-login.php).

== Changelog ==

Review the Sidejump changelog here: [http://sidejump.net/documentation-and-resources](http://sidejump.net/documentation-and-resources).

== Upgrade Notice ==

No upgrade notices are available at this time.