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
 
// Include local configuration
if (file_exists(dirname(__FILE__) . '/local-config.php')) {
	include(dirname(__FILE__) . '/local-config.php');
}

// Global DB config
if (!defined('DB_NAME')) {
	define('DB_NAME', 'bo-ru');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'bo-ru');
}
if (!defined('DB_PASSWORD')) {
	define('DB_PASSWORD', '4161isitossett2395');
}
if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
}

/** Database Charset to use in creating database tables. */
if (!defined('DB_CHARSET')) {
	define('DB_CHARSET', 'utf8');
}

/** The Database Collate type. Don't change this if in doubt. */
if (!defined('DB_COLLATE')) {
	define('DB_COLLATE', '');
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'YnFQmweK.W|Bs<(ioE~?3_r~s1%]|fSPM7Pyj!nv-SM&i~k3sS^$^abC p+z/DRo');
define('SECURE_AUTH_KEY',  'fsD$GeuZ@-~{~e,8E1B`Z7BgHj|8#&[_pK9U<2wIXRd-M%<br`so5[8GOEL4p+#a');
define('LOGGED_IN_KEY',    '-yoIJ0b4M:|I9p4F+DRTs+mfNlw{%.25(0KhPgOsS:dPY1WLF@=v[80(P3O>7Kkz');
define('NONCE_KEY',        '/jO<x6dRWcw8KQ 8J!5x}+dY5jx>AQk| <U4Skbx.p]o0{k@{l?-O_1Wlj-U(M|s');
define('AUTH_SALT',        't([9vA_EI}C]i2<:XjGSHZs*uI;rG/|XGrBSQ0GFM^(Z@VP4wA;M3wsZD!``&NfP');
define('SECURE_AUTH_SALT', '?l?P{xO4[mr6|M<.,0f0+ud~i|ET4NS#BBi#k+q>6}=Uop%i+d+P68wFWuSn^Mw@');
define('LOGGED_IN_SALT',   'q:myrj=.M~$j0vDcfyaxwgn|^,L l@`VPA~$6xe8W[_L,<.Z:GN}#,9oj#(RNkf{');
define('NONCE_SALT',       'uS?UkiGEP+m5?3Ots|U&c[.l<BdYBh`$RkOQMQ@zZdG.-fd&R;W^/s2*{=c4^jG&');

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
 * Set custom paths
 *
 * These are required because wordpress is installed in a subdirectory.
 */
if (!defined('WP_SITEURL')) {
	define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/wordpress');
}
if (!defined('WP_HOME')) {
	define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME'] . '');
}
if (!defined('WP_CONTENT_DIR')) {
	define('WP_CONTENT_DIR', dirname(__FILE__) . '/content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/content');
}


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
if (!defined('WP_DEBUG')) {
	define('WP_DEBUG', false);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
