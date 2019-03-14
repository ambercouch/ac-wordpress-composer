<?php

// Define Environments
$environments = array(
    'local' => '.local',
    'development' => ['.test', '.dev','.sta', 'dev.', 'staging.'],
    'production' => ['.prod','.live'],
    //'another_env' = ['ano.','.ano']
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

if (in_array(ENVIRONMENT, ['production','development','local']) ){
    //define('ACT_CONTENT', '/wp-content/' . ENVIRONMENT);
    define('ACT_CONTENT', '/wp-content');

}else{
    //define('ACT_CONTENT', '/wp-content');
    define('ACT_CONTENT', '/wp-content/' . ENVIRONMENT);
}


