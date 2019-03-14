<?php

// Include ENVIRONMENT setup
if (file_exists(dirname(__FILE__) . '/ac-config-environment.php')) {
  include(dirname(__FILE__) . '/ac-config-environment.php');
}

// Include ENVIRONMENT config
if (file_exists(dirname(__FILE__) . '/ac-config-' . ENVIRONMENT . '.php')) {
  include(dirname(__FILE__) . '/ac-config-' . ENVIRONMENT . '.php');
} else {
  // Error message and die
  echo 'We can\'t find the config file for this environment  : ' . dirname(__FILE__) . '/ac-config-' . ENVIRONMENT . '.php';
  die();
}

// Include wrapper config file
if (file_exists(dirname(__FILE__) . '/ac-config-wrapper.php')) {
  include(dirname(__FILE__) . '/ac-config-wrapper.php');
} else {
  // Error message and die
  echo 'We can\'t find the file ac-config-wrapper.php ';
  die();
}

/* wp-contact-from-7 */
define ('WPCF7_AUTOP', false );   // set to false to remove <br> tags

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
