<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ppo');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-P.imAmjS*au)IJ6m:VPGD<);|euCvHgj{@o0Y)`Afh4%wgK.oR-B-`T+F-6lGL,');
define('SECURE_AUTH_KEY',  'S3k;EbV-UeW]km+-h8Wf$E9OcMb:iIhX!X;t;.aX4,j45P}5+FA8U0i4Qcr[)Ye+');
define('LOGGED_IN_KEY',    'j{6sA,@{Nq|QUwz7!Ld20W<mQG7L];?Hv!9=IQ^Z?S[,qa$Bhj>!`Q_5ZM0v@SMR');
define('NONCE_KEY',        'UQxG$1~k`?{Jj.R5x4~Fv/Jj-a+2Kp+R- E1ihdhMJ5hpR$^|#N_}2SVT?2b}]g4');
define('AUTH_SALT',        'H,Y;~[+NWk&XB+j4s:|Q0ZP(OGHnfEGA7 t*pWQvdrHnZ@Ve;-`.jbTWqR/+2q)L');
define('SECURE_AUTH_SALT', '=gYUh6ym:eqG59iw-?Qf}q><~ZD|~wMLjic^<9l&+|*zK/QhV[n_?V@6]:nTwIl-');
define('LOGGED_IN_SALT',   'aZSQDX<mW<PE-1?.*9],i6|ZBc>8-VH]s{PabYu>m:BOGbM8F8++p9e<E]|P%T9m');
define('NONCE_SALT',       'H|`aVo`7=iCPpcNVrX@=/_Dl{%4|2soR$!HUB+>[_0TBkV8t_q1=<=q:L[}t-CV+');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
