<?php

/**
 * Determine protocol
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))
    ? "https://"
    : "http://";

/**
 * Determine server name, with EB fallback
 */
$server_name = '';

if (!empty($_SERVER['SERVER_NAME'])) {
    $server_name = $_SERVER['SERVER_NAME'];
} elseif (getenv('WP_DOMAIN')) {
    $server_name = getenv('WP_DOMAIN');
}

// Strip 'www.' if present
if (stripos($server_name, 'www.') === 0) {
    $server_name = substr($server_name, 4);
}

/**
 * Set custom paths for subdirectory WP install
 */
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', $protocol . $server_name . '/cms');
}
if (!defined('WP_HOME')) {
    define('WP_HOME', $protocol . $server_name);
}
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', dirname(__FILE__) . ACT_CONTENT);
}
if (!defined('WP_CONTENT_URL')) {
    define('WP_CONTENT_URL', $protocol . $server_name . ACT_CONTENT);
}

