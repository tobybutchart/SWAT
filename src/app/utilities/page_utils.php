<?php
    namespace app\page_utils;

    include_once "./defs/swat.php";
    include_once "./app/utilities/server_utils.php";
    include_once "./app/utilities/session_utils.php";
    include_once "./app/classes/endpoint.php";
    include_once "./app/classes/config.php";
    include_once "./app/classes/log.php";

    function throw_404(){
        $uri = \app\server_utils\get_uri();
        header("LOCATION: /404?uri=$uri");
    }

    function map_uri_to_page(){
        $uri = \app\server_utils\get_uri();

        if($uri === ""){
            $page = \app\server_utils\file_with_path("pages", "index");
        }else{
            $page = \app\server_utils\file_with_path("pages", $uri);
        }

        if(file_exists($page)){
            $head = \app\server_utils\file_with_path("components", "header");
            $foot = \app\server_utils\file_with_path("components", "footer");
            include $head;
            include $page;
            include $foot;

            return true;
        }

        return false;
    }

    function ini_get_val(string $key, array $global_array, array $page_array){
        for ($i = 0; $i <= 1; $i++) {
            switch ($i) {
                case 0:
                    $a = $page_array;
                    break;
                case 1:
                    $a = $global_array;
                    break;
                default:
                    $a = [];
            }

            if (array_key_exists($key, $a) && !empty($a[$key])){
                return $a[$key];
            }
        }

        return '';
    }

    function head_get_val(string $meta_tag, string $key, array $global_array, array $page_array, string $base_path = ''){
        $s = ini_get_val($key, $global_array, $page_array);

        if (!empty($s)){
            return HEAD_SPACE.str_replace(REPLACE_ME, $base_path.$s, $meta_tag).PHP_EOL;
        }

        return '';
    }

    function head_get_contents(){
        $uri = \app\server_utils\get_uri();

        $versions = new \app\config\config("versions", $uri);

        $ini = parse_ini_file("./config/pages.ini", true);

        $global_head = array_key_exists("global", $ini) ? $ini["global"] : [];
        $page_head = array_key_exists($uri, $ini) ? $ini[$uri] : [];

        $base = array_key_exists('base', $global_head) ? $global_head["base"] : "";
        $head = '';

        //not configurable
        $head .= HEAD_SPACE.'<meta charset="utf-8">'.PHP_EOL;
        $head .= head_get_val('<meta http-equiv="Content-Security-Policy" content="default-src \'self\'">', 'content-security-policy', $global_head, $page_head);
        $head .= HEAD_SPACE.'<meta name="viewport" content="width=device-width, initial-scale=1">'.PHP_EOL;
        $head .= HEAD_SPACE.'<meta name="SWAT-version" content="'.SWAT_VERSION.'">'.PHP_EOL;

        //configurable
        $head .= head_get_val('<title>'.REPLACE_ME.'</title>', 'title', $global_head, $page_head);
        $head .= head_get_val('<base href="'.REPLACE_ME.'">', 'base', $global_head, $page_head);
        $head .= head_get_val('<meta name="author" content="'.REPLACE_ME.'">', 'author', $global_head, $page_head);
        $head .= head_get_val('<meta name="description" content="'.REPLACE_ME.'">', 'description', $global_head, $page_head);
        $head .= head_get_val('<meta name="subject" content="'.REPLACE_ME.'">', 'subject', $global_head, $page_head);
        $head .= head_get_val('<meta name="application-name" content="'.REPLACE_ME.'">', 'application-name', $global_head, $page_head);

        //no index
        $head .= head_get_val('<meta name="robots" content="noindex">', 'no-index', $global_head, $page_head);
        $head .= head_get_val('<meta name="googlebot" content="noindex">', 'no-index', $global_head, $page_head);

        //android
	    $head .= head_get_val('<meta name="theme-color" content="'.REPLACE_ME.'">', 'theme-color', $global_head, $page_head);

        //microsoft
	    $head .= head_get_val('<meta name="msapplication-TileColor" content="'.REPLACE_ME.'">', 'theme-color', $global_head, $page_head);

        //facebook
	    $head .= HEAD_SPACE.'<meta property="og:url" content="'.$uri.'">'.PHP_EOL;
        $head .= HEAD_SPACE.'<meta property="og:locale" content="en">'.PHP_EOL;
    	$head .= head_get_val('<meta property="og:type" content="'.REPLACE_ME.'">', 'type', $global_head, $page_head);
    	$head .= head_get_val('<meta property="og:title" content="'.REPLACE_ME.'">', 'title', $global_head, $page_head);
    	$head .= head_get_val('<meta property="og:image" content="'.REPLACE_ME.'">', 'thumbnail-image', $global_head, $page_head, $base);
    	$head .= head_get_val('<meta property="og:image:alt" content="'.REPLACE_ME.'">', 'thumbnail-image-alt', $global_head, $page_head);
    	$head .= head_get_val('<meta property="og:description" content="'.REPLACE_ME.'">', 'description', $global_head, $page_head);
    	$head .= head_get_val('<meta property="og:site_name" content="'.REPLACE_ME.'">', 'application-name', $global_head, $page_head);
    	$head .= head_get_val('<meta property="article:author" content="'.REPLACE_ME.'">', 'author', $global_head, $page_head);

	    //twitter
        $head .= HEAD_SPACE.'<meta name="twitter:url" content="'.$uri.'">'.PHP_EOL;
    	$head .= head_get_val('<meta name="twitter:title" content="'.REPLACE_ME.'">', 'title', $global_head, $page_head);
    	$head .= head_get_val('<meta name="twitter:card" content="'.REPLACE_ME.'">', 'twitter-card', $global_head, $page_head);
    	$head .= head_get_val('<meta name="twitter:site" content="'.REPLACE_ME.'">', 'twitter-site', $global_head, $page_head);
    	$head .= head_get_val('<meta name="twitter:creator" content="'.REPLACE_ME.'">', 'twitter-creator', $global_head, $page_head);
    	$head .= head_get_val('<meta name="twitter:description" content="'.REPLACE_ME.'">', 'description', $global_head, $page_head);
    	$head .= head_get_val('<meta name="twitter:image" content="'.REPLACE_ME.'">', 'thumbnail-image', $global_head, $page_head, $base);
    	$head .= head_get_val('<meta name="twitter:image:alt" content="'.REPLACE_ME.'"> ', 'thumbnail-image-alt', $global_head, $page_head);

        //icons apple
        $head .= head_get_val('<link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.REPLACE_ME.'">', 'icon-144x144', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'.REPLACE_ME.'">', 'icon-114x114', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'.REPLACE_ME.'"> ', 'icon-72x72', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="apple-touch-icon-precomposed" href="'.REPLACE_ME.'">', 'icon-32x32', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="apple-touch-icon" sizes="180x180" href="'.REPLACE_ME.'">', 'icon-180x180', $global_head, $page_head, $base);

        //icons microsoft
        $head .= head_get_val('<meta name="msapplication-TileImage" content="'.REPLACE_ME.'">', 'x', $global_head, $page_head, $base);

        //icons other
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="16x16">', 'icon-16x16', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="32x32">', 'icon-32x32', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="48x48">', 'icon-48x48', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="64x64">', 'icon-64x64', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="72x72">', 'icon-72x72', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="96x96">', 'icon-96x96', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="114x114">', 'icon-114x114', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="144x144">', 'icon-144x144', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="180x180">', 'icon-180x180', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="192x192">', 'icon-192x192', $global_head, $page_head, $base);
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'" sizes="512x512">', 'icon-512x512', $global_head, $page_head, $base);

        //icons shortcut
        $head .= head_get_val('<link rel="shortcut icon" href="'.REPLACE_ME.'">', 'favicon-png', $global_head, $page_head, $base);

         //Major Browsers
        $head .= head_get_val('<link rel="icon" type="image/png" href="'.REPLACE_ME.'">', 'favicon-png', $global_head, $page_head, $base);
        //Internet Explorer
        $head .= head_get_val('<!--[if IE]><link rel="SHORTCUT ICON" href="'.REPLACE_ME.'"/><![endif]-->', 'favicon-ico', $global_head, $page_head, $base);

        //css files
        $css = array_key_exists('css', $global_head) ? $global_head['css'] : [];
        array_merge($css, array_key_exists('css', $page_head) ? $page_head['css'] : []);

        foreach ($css as $file) {
            $head .= HEAD_SPACE.'<link rel="stylesheet" href="'.$base.$file.'?v='.$versions->get_val('css').'">'.PHP_EOL;
        }

        return $head;
    }

    function foot_get_contents(){
        $uri = \app\server_utils\get_uri();

        $versions = new \app\config\config("versions", $uri);

        $ini = parse_ini_file("./config/pages.ini", true);

        $global_head = $ini["global"];
        $page_head = array_key_exists($uri, $ini) ? $ini[$uri] : [];

        $base = array_key_exists('base', $global_head) ? $global_head["base"] : "";
        $foot = '';

        //js files
        $js = array_key_exists('js', $global_head) ? $global_head['js'] : [];
        array_merge($js, array_key_exists('js', $page_head) ? $page_head['js'] : []);

        foreach ($js as $file) {
            $foot .= HEAD_SPACE.'<script type="text/javascript" src="'.$base.$file.'?v='.$versions->get_val('js').'" defer></script>'.PHP_EOL;
        }

        return $foot;
    }

    function nav_get_class(string $page_name){
        $uri = $uri = \app\server_utils\get_uri();

        if(strtolower($page_name) === "logout"){
            return \app\session_utils\is_user_logged_in(-1) ? "" : "disabled";
        }else{
            return strtolower($uri) === strtolower($page_name) ? "active" : "";
        }
    }

    function check_login(){
        $uri = \app\server_utils\get_uri(false, false);
        $is_login_page = $uri == 'login';

        if($is_login_page){
            $uri = empty(\app\server_utils\uri_from_query_string()) ? $uri : \app\server_utils\uri_from_query_string();
        }

        $config = new \app\config\config("pages", $uri);
        $auth = $config->get_val('authenticate');
        $user_access_level = $config->get_val('user_access_level', -1);

        if(!empty($auth)){
            $user = $config->get_val('username');
            $pass = $config->get_val('password');

            $remember_me = false;
            $valid_credentials = false;
        	$is_logged_in = \app\session_utils\is_user_logged_in($user_access_level);

            //if from login form
            if(isset($_POST["swat-login-btn"])) {
                $endpoint = new \app\endpoint\endpoint();
                $basic_auth_config = new \app\config\config("apis", "global");
                $main_config = new \app\config\config("config", "main");

                $body = [];
                $body['username'] = $_POST["swat-login-username"];
                $body['password'] = $_POST["swat-login-password"];

                $endpoint->set_username($basic_auth_config->get_val('basic_auth_username'));
                $endpoint->set_password($basic_auth_config->get_val('basic_auth_password'));
                $endpoint->set_url($main_config->get_val('base_url').'/api/users/get');
                $endpoint->set_body($body);

                try{
                    $endpoint->execute();
                    $valid_credentials = $endpoint->get_val('valid_credentials');
                }catch(\Exception $e){
                    $msg = sprintf(
                        "Message: %s Code: %c File: %s Line: %c",
                        $e->getMessage(),
                        $e->getCode(),
                        $e->getFile(),
                        $e->getLine()
                    );

                    $log = new \app\logging\log(DOCUMENT_ROOT.'\logs', 'error', \app\logging\log_level::all, false);
                    $log->log("Error caught: {$msg}", \app\logging\log_type::error);
                }

                $remember_me = isset($_POST['swat-login-remember-me']);
            }

        	if ($valid_credentials || $is_logged_in) {
                \app\session_utils\log_user_in($user_access_level, true);

                if($is_login_page){
                    header("LOCATION: /$uri");
                    return true;
                }
        	}else{
                if(!$is_login_page){
                    header("LOCATION: /Login?uri=$uri");
                    return true;
                }
            }
        }

        return false;
    }
