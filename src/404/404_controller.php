<?php
    include_once "./app/classes/log.php";
    include_once "./app/utilities/file_utils.php";
    include_once "./app/utilities/server_utils.php";
    include_once "./defs/swat.php";

    header('HTTP/1.1 404 Not Found');

    $head = app\server_utils\file_with_path("components", "header");
    $foot = app\server_utils\file_with_path("components", "footer");

    $url = app\server_utils\get_url();

    $log = new app\logging\log(DOCUMENT_ROOT.'\logs', '404-page', app\logging\log_level::all, false);
    $log->log("404 error caught: {$url}", app\logging\log_type::error);

    include $head;
?>
            <div class="container h-swat">
                <div class="row h-swat">
                    <div class="col-md"></div>
                    <div class="col-12 col-md-6 h-90 align-middle">
                        <div class="container-fluid swat-jumbo-top">
                            <img class="img-fluid" src="/web/img/404.png" alt="404">
                            <p class="fs-4">This page cannot be found...</p>
                        </div>
                    </div>
                    <div class="col-md"></div>
                </div>
            </div>
<?php
    include $foot;
