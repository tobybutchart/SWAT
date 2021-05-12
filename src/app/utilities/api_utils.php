<?php
    namespace app\api_utils;

    include_once "./defs/swat.php";
    include_once "./app/classes/http_response_codes.php";
    include_once "./app/classes/config.php";
    include_once "./app/classes/api_log.php";
    include_once "./app/utilities/server_utils.php";

/**
 *  returns true is requested URI is to be handled as an internal API
 *
 *  @return     boolean     true if API
 */
    function is_api(){
        $uri = \app\server_utils\get_uri();
        $a = explode("/", $uri);
        return (count($a) > 0 && $a[0] == "api");
    }

/**
 *  returns true if requested URI can be mapped to an internal API
 *
 *  @return     boolean     true if API is mappable
 */
    function map_uri_to_api(){
        $uri = \app\server_utils\get_uri();

        $api = $uri.'.php';

        if(file_exists($api)){
            try{
                $GLOBALS['request'] = [];
                $GLOBALS['response'] = "";

                $api_config = new \app\config\config("apis", $api);

                $l = new \app\api_logging\api_log();
                $l->log_request();

                header("SWAT-version: ".SWAT_VERSION);
                header("GUID: {$l->get_guid()}");

                $dump = file_get_contents('php://input');

                $GLOBALS['request']['method'] = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
                $GLOBALS['request']['content_type'] = filter_input(INPUT_SERVER, 'CONTENT_TYPE', FILTER_SANITIZE_STRING);
                $GLOBALS['request']['body'] = json_decode($dump, true);
                $GLOBALS['request']['username'] = $_SERVER['PHP_AUTH_USER'];
                $GLOBALS['request']['password'] = $_SERVER['PHP_AUTH_PW'];
                $GLOBALS['request']['length'] = (int) $_SERVER['CONTENT_LENGTH'];

                //check method/verb
                if($GLOBALS['request']['method'] != 'POST'){
                    serve_invalid_request(\app\http_response_codes\http_response_code::method_not_allowed, $l);
                }

                //check content type
                if($GLOBALS['request']['content_type'] != 'application/json'){
                    serve_invalid_request(\app\http_response_codes\http_response_code::unsupported_media_type, $l);
                }

                //check json body
                if(!\app\json_utils\is_valid_json($dump) || strlen($dump) != $GLOBALS['request']['length'] || !check_required_fields($api_config->get_array('required_fields'), $GLOBALS['request']['body'])){
                    serve_invalid_request(\app\http_response_codes\http_response_code::bad_request, $l);
                }

                //api logic
                include $api;

                $l->log_response(\app\http_response_codes\http_response_code::ok, $GLOBALS['response'] );
                echo $GLOBALS['response'] ;
                return true;
            }catch(\Exception $e){
                $l->log_error(\app\http_response_codes\http_response_code::internal_error, $GLOBALS['response'] , $e);
                throw_error(\app\http_response_codes\http_response_code::internal_error);
                die();
            }
        }

        return false;
    }

/**
 *  serves http response code, and logs response
 *
 *  @param     int                        $http_response_code    http response code to be served. i.e. 404, 500, etc.
 *  @param     app\api_logging\api_log    $api_log               instance of api_log
 *  @return    void
 */
    function serve_invalid_request(int $http_response_code, \app\api_logging\api_log $api_log){
        $http_response_code = new \app\http_response_codes\http_response_code($http_response_code);
        $api_log->log_response($http_response_code->http_response_code, $http_response_code->to_string());
        throw_error($http_response_code->http_response_code);
        die();
    }

/**
 *  checks API request contains required fields
 *
 *  @param     array     $required_fields    array of required fields
 *  @param     array     $request            request body
 *  @return    boolean                       true if all fields exist
 */
    function check_required_fields(array $required_fields, array $request){
        foreach ($required_fields as $required_field){
            if(!array_key_exists($required_field, $request)){
                return false;
            }
        }

        return true;
    }

/**
 *  creates instance of http_response_code, sets headers, etc.
 *
 *  @param     int       $http_response_code    http response code to serve
 *  @return    void
 */
    function throw_error(int $http_response_code){
        $rc = new \app\http_response_codes\http_response_code($http_response_code);
        $rc->set_header();
        echo $rc->to_string();
    }

/**
 *  returns true if basic authentication is needed,isn't supplied, or is incorrect
 *
 *  @return    boolean    
 */
    function failed_authentication(){
        $uri = \app\server_utils\get_uri(false, true);

        $config = new \app\config\config("apis", $uri);
        $auth = $config->get_val('authenticate');

        if(!empty($auth)){
            //check credentials
            $user = $config->get_val('basic_auth_username');
            $pass = $config->get_val('basic_auth_password');

        	header('Cache-Control: no-cache, must-revalidate, max-age=0');

        	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']));

            $is_not_authenticated = (
        		!$has_supplied_credentials ||
                empty($user) ||
                empty($pass) ||
        		$_SERVER['PHP_AUTH_USER'] != $user ||
        		$_SERVER['PHP_AUTH_PW']   != $pass
        	);

        	if ($is_not_authenticated) {
        		header('WWW-Authenticate: Basic realm="Access denied"');
                throw_error(\app\http_response_codes\http_response_code::unauthorized);
        		return true;
        	}
        }

        return false;
    }
