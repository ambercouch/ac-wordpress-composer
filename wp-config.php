<?php

// Include ENVIRONMENT
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
