<?php
    $path = __DIR__;
    set_include_path($path);

    include_once "./app/utilities/server_utils.php";
    include_once "./app/utilities/api_utils.php";
    include_once "./app/utilities/page_utils.php";
    include_once "./app/utilities/session_utils.php";
    include_once "./app/classes/http_response_codes.php";

    //api
    if (app\api_utils\is_api()){
        if (app\api_utils\failed_authentication()){
            die();
        }
        if (app\server_utils\map_uri_to_controller()){
            die();
        }
        if (app\api_utils\map_uri_to_api()){
            die();
        }
        app\api_utils\throw_error(app\http_response_codes\http_response_code::not_found);
        die();
    }

    //makes sure all pages have a valid session
    app\session_utils\start_session();

    //page
    if (app\page_utils\check_login()){
        die();
    }
    if (app\server_utils\map_uri_to_controller()){
        die();
    }
    if (app\page_utils\map_uri_to_page()){
        die();
    }
    app\page_utils\throw_404();
    die();
