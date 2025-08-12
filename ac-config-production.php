<?php

// Force early logging no matter what
@ini_set('log_errors', '1');
@ini_set('error_reporting', E_ALL);
@ini_set('error_log', '/tmp/php-app.log');

// Sanity ping so we know this file ran
error_log('AC production config loaded at ' . gmdate('c'));

define('DB_NAME', $_SERVER['RDS_DB_NAME']);
define('DB_USER', $_SERVER['RDS_USERNAME']);
define('DB_PASSWORD', $_SERVER['RDS_PASSWORD']);
define('DB_HOST', $_SERVER['RDS_HOSTNAME'] . ':' . $_SERVER['RDS_PORT']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('AUTH_KEY',         $_SERVER['AUTH_KEY']);
define('SECURE_AUTH_KEY',  $_SERVER['SECURE_AUTH_KEY']);
define('LOGGED_IN_KEY',    $_SERVER['LOGGED_IN_KEY']);
define('NONCE_KEY',        $_SERVER['NONCE_KEY']);
define('AUTH_SALT',        $_SERVER['AUTH_SALT']);
define('SECURE_AUTH_SALT', $_SERVER['SECURE_AUTH_SALT']);
define('LOGGED_IN_SALT',   $_SERVER['LOGGED_IN_SALT']);
define('NONCE_SALT',       $_SERVER['NONCE_SALT']);


// Respect HTTPS behind CloudFront/ALB without notices
$proto_cf  = $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] ?? null;
$proto_fwd = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
if ($proto_cf === 'https' || $proto_fwd === 'https') {
    $_SERVER['HTTPS'] = 'on';
    if (!defined('FORCE_SSL_ADMIN')) define('FORCE_SSL_ADMIN', true);
}

/**
 *
 * WordPress Database Table prefix some test.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', '/tmp/wp-debug.log'); // <— force EB-safe log path
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 0);

define('DISALLOW_FILE_MODS', true);
define('AUTOMATIC_UPDATER_DISABLED', true);

/* WP Memory Limit */
define('WP_MEMORY_LIMIT', '512M');
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
