<?php

// Include ENVIRONMENT setup
if (file_exists(dirname(__FILE__) . '/env-config.php')) {
  include(dirname(__FILE__) . '/env-config.php');
}

// Include ENVIRONMENT config
if (file_exists(dirname(__FILE__) . '/' . ENVIRONMENT . '-config.php')) {
  include(dirname(__FILE__) . '/' . ENVIRONMENT . '-config.php');
} else {
  // Error message anf die
  echo 'We can\'t find the config file for this environment  : ' . dirname(__FILE__) . '/' . ENVIRONMENT . '-config.php';
  die();
}

// Include wrapper config file
if (file_exists(dirname(__FILE__) . '/wrapper-config.php')) {
  include(dirname(__FILE__) . '/wrapper-config.php');
} else {
  // Error message anf die
  echo 'We can\'t find the file wrapper-config.php ';
  die();
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
