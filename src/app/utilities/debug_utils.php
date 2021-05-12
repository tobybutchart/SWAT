<?php

    namespace app\debug_utils;

    function server_dump(){
        $server_keys = array(
            'AUTH_TYPE',
            'DOCUMENT_ROOT',
            'GATEWAY_INTERFACE',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'ORIG_PATH_INFO',
            'PATH_INFO',
            'PATH_TRANSLATED',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_PW',
            'PHP_AUTH_USER',
            'PHP_SELF',
            'QUERY_STRING',
            'REDIRECT_REMOTE_USER',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'REQUEST_URI',
            'SCRIPT_FILENAME',
            'SCRIPT_NAME',
            'SERVER_ADDR',
            'SERVER_ADMIN',
            'SERVER_NAME',
            'SERVER_PORT',
            'SERVER_PROTOCOL',
            'SERVER_SIGNATURE',
            'SERVER_SOFTWARE'
        );

        $server_filt = filter_input_array(INPUT_SERVER);

        echo '<table cellpadding="10">';
        foreach ($server_keys as $key){
            if (isset($server_filt[$key])){
                echo '<tr><td>'.$key.'</td><td>'.$server_filt[$key].'</td></tr>' ;
            }
            else{
                echo '<tr><td>'.$key.'</td><td>-</td></tr>' ;
            }
        }
        echo '</table>';
    }

    function post_dump(){
        pretty_dump($_POST);
    }

    function session_dump(){
        pretty_dump($_SESSION);
    }

    function pretty_dump($var){
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
