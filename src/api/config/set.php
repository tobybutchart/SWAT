<?php
    include_once "./app/classes/config.php";

    $config_to_edit = new app\config\config($GLOBALS['request']['body']['file_name'], "");
    $file_updated = $config_to_edit->set_contents($GLOBALS['request']['body']['contents']);

    $GLOBALS['response'] = '{"file_updated":'.$file_updated.',"errors":""}';
