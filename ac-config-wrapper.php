<?php

/**
 * Test ssl / https
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

/**
 * Set custom paths
 *
 * These are required because wordpress is installed in a subdirectory.
 */
if (!defined('WP_SITEURL')) {
  define('WP_SITEURL', $protocol . $_SERVER['SERVER_NAME'] . '/cms');
}
if (!defined('WP_HOME')) {
  define('WP_HOME', $protocol . $_SERVER['SERVER_NAME'] . '');
}
if (!defined('WP_CONTENT_DIR')) {
  define('WP_CONTENT_DIR', dirname(__FILE__) . ACT_CONTENT);
}
if (!defined('WP_CONTENT_URL')) {
  define('WP_CONTENT_URL', $protocol . $_SERVER['SERVER_NAME'] . ACT_CONTENT);
}
if (!defined('UPLOADS')) {
  define('UPLOADS', '..'.ACT_CONTENT.'/uploads');
}