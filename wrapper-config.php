<?php

/**
 * Set custom paths
 *
 * These are required because wordpress is installed in a subdirectory.
 */
if (!defined('WP_SITEURL')) {
  define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/cms');
}
if (!defined('WP_HOME')) {
  define('WP_HOME', 'http://' . $_SERVER['SERVER_NAME'] . '');
}
if (!defined('WP_CONTENT_DIR')) {
  define('WP_CONTENT_DIR', dirname(__FILE__) . '/wp-content');
}
if (!defined('WP_CONTENT_URL')) {
  define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/wp-content');
}
if (!defined('UPLOADS')) {
  define('UPLOADS', '../wp-content/uploads');
}
