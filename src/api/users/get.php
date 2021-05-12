<?php
    include_once "./app/classes/config.php";

    $config = new app\config\config("credentials", "admin");

    $valid_credentials = (
        //body
        isset($GLOBALS['request']['body']) &&
        //username
        isset($GLOBALS['request']['body']['username']) &&
        !empty($GLOBALS['request']['body']['username']) &&
        $GLOBALS['request']['body']['username'] === $config->get_val('username') &&
        //password
        isset($GLOBALS['request']['body']['password']) &&
        !empty($GLOBALS['request']['body']['password']) &&
        $GLOBALS['request']['body']['password'] === $config->get_val('password')
    );

    //for testing
    //throw new Exception('TEST ERROR');

    $username = isset($GLOBALS['request']['body']['username']) ? $GLOBALS['request']['body']['username'] : "";
    $bool = $valid_credentials ? 'true' : 'false';
    $GLOBALS['response'] = '{"username":"'.$username.'","valid_credentials":'.$bool.',"user_access_level":-1}';
