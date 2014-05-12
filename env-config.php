<?php

// Define Environments
$environments = array(
    'local' => '.local',
    'development' => 'dev.'
);
// Get Server name
$server_name = $_SERVER['SERVER_NAME'];

foreach ($environments AS $key => $env) {
  if (strstr($server_name, $env)) {
    define('ENVIRONMENT', $key);
    break;
  }
}

// If no environment is set default to production
if (!defined('ENVIRONMENT')) {
  define('ENVIRONMENT', 'production');
}

