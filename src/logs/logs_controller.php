<?php
    include_once "./app/utilities/server_utils.php";
    include_once "./app/utilities/file_utils.php";
    include_once "./app/classes/file_list_types.php";

    $head = app\server_utils\file_with_path("components", "header");
    $foot = app\server_utils\file_with_path("components", "footer");

    include $head;
?>
    <div class="container h-swat swat-log-viewer" >
        <div class="row h-swat">
            <div class="col-sm-12 col-md-6 col-lg-3 h-swat">
                <?php
                    $base = __DIR__;
                    $url = "/Logs";

                    $dirs = app\file_utils\file_list_to_array($base, app\file_list_types\file_list_type::dirs_only, "log", false);
                    app\file_utils\array_to_dropdown($dirs, $url);

                    $d = app\file_utils\file_from_query_string(app\file_list_types\file_list_type::dirs_only);

                    //stops loading all files on initial page load
                    if(!empty($d)){
                        $files = app\file_utils\file_list_to_array($base.$d, app\file_list_types\file_list_type::files_only, "log", false);
                        app\file_utils\array_to_list_group($files, $url);
                    }
                ?>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-9 h-swat">
                <?php
                    $f = app\file_utils\file_from_query_string(app\file_list_types\file_list_type::files_only);
                    app\file_utils\display_log_file($base, $d, $f);
                ?>
            </div>
        </div>
    </div>
<?php
    include $foot;
