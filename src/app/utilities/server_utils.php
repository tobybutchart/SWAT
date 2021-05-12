<?php
    namespace app\server_utils;

    include_once "./defs/swat.php";
    //include_once "./app/classes/config.php";

    function get_val(string $key){
        return filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_URL);
    }

    function get_url(){
        $host = get_val("HTTP_HOST");
        $uri = get_val("REQUEST_URI");

        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$host}{$uri}";
    }

    function get_uri(bool $last_path = false, bool $remove_base = false){
        $config = new \app\config\config("config", "main");

        $uri = get_val("REQUEST_URI");
        $uri = strtolower($uri);
        $uri = str_replace($config->get_val('nested_base'), "", $uri);
        $uri = strtok($uri, "?");
        $uri = trim($uri, "/");

        if($last_path || $remove_base){
            $a = explode("/", $uri);

            if(count($a) > 0){
                if($last_path){
                    $uri = $a[count($a) - 1];
                }
                if($remove_base){
                    unset($a[0]);
                    $uri = implode("/", $a);
                }
            }
        }

        return $uri;
    }

    function uri_from_query_string(){
        $s = '';
        $query_strings = '';

        $query_string = get_val("QUERY_STRING");
        parse_str($query_string, $query_strings);

        if (array_key_exists('uri', $query_strings) && !empty($query_strings['uri'])){
            $s = $query_strings['uri'];
        }

        return $s;
    }

    function file_with_path(string $initial_dir, string $page){
        $return = DOCUMENT_ROOT."\\$initial_dir\\$page.php";
        return str_replace("/", "\\", $return);
    }

    function map_uri_to_controller(bool $is_main = false){
        $full_uri = get_uri();
        $last_path = get_uri(true, false);

        $page = file_with_path($full_uri, $last_path."_controller");
//var_dump($page);
// var_dump(__DIR__);
        if(file_exists($page)){
            include $page;
            return true;
        }

        return false;
    }
