<?php

// Define Environments
$environments = array(
    'local' => '.local',
    'development' => ['.test', '.dev','.sta'],
    'production' => ['.prod','.live']
);
// Get Server name
$server_name = $_SERVER['SERVER_NAME'];

function server_name_env($env, $server_name){
    if (strstr($server_name, $env)) {
        return true;
    }else{
        return false;
    }
}

foreach ($environments AS $key => $env) {

    $env_array = (is_array($env)) ? $env : [$env];

    foreach ($env_array as $env){

        if(server_name_env($env, $server_name)){
            define('ENVIRONMENT', $key);
            break;
        };

    }

}

// If no environment is set default to production
if (!defined('ENVIRONMENT')) {
  define('ENVIRONMENT', 'production');
}

