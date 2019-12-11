<?php

/**
 * Test ssl / https
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$server_name = $_SERVER['SERVER_NAME'];

if (substr( $server_name, 0, 4) === "www."){
    $server_name = ltrim($server_name, 'www.');
}

/**
 * Set custom paths
 *
 * These are required because wordpress is installed in a subdirectory.
 */
if (!defined('WP_SITEURL')) {
  define('WP_SITEURL', $protocol . $server_name  . '/cms');
}
if (!defined('WP_HOME')) {
  define('WP_HOME', $protocol . $server_name  . '');
}
if (!defined('WP_CONTENT_DIR')) {
  define('WP_CONTENT_DIR', dirname(__FILE__) . ACT_CONTENT);
}
if (!defined('WP_CONTENT_URL')) {
  define('WP_CONTENT_URL', $protocol . $server_name  . ACT_CONTENT);
}
//if (!defined('UPLOADS')) {
//  define('UPLOADS', '..'.ACT_CONTENT.'/uploads');
//}
