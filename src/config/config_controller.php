<?php
    include_once "./app/utilities/server_utils.php";
    include_once "./app/utilities/file_utils.php";
    include_once "./app/classes/file_list_types.php";
    include_once "./app/classes/config.php";

    $head = app\server_utils\file_with_path("components", "header");
    $foot = app\server_utils\file_with_path("components", "footer");

    include $head;
?>
    <div class="container h-swat swat-log-viewer" >
        <div class="row h-swat">
            <div class="col-sm-12 col-md-6 col-lg-3 h-swat">
                <?php
                    $base = __DIR__;
                    $url = "/Config";
                    $d = "\\";

                    $files = app\file_utils\file_list_to_array($base.$d, app\file_list_types\file_list_type::files_only, "ini", true);
                    app\file_utils\array_to_list_group($files, $base, $url);
                ?>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-9 h-swat">
                <?php
                    $f = app\file_utils\file_from_query_string($base, app\file_list_types\file_list_type::files_only);
                    app\file_utils\display_ini_file($base, $d, $f, $url);
                ?>
                <br>
                <button class="btn btn-swat btn-sm btn-swat-save-fr" id="swat-config-save-contents" onclick="saveConfig()">Save</button>
            </div>
        </div>
    </div>
<?php
    include $foot;
    $f = pathinfo($f, PATHINFO_FILENAME);
    $basic_auth_config = new \app\config\config("apis", "global");
    $main_config = new \app\config\config("config", "main");
?>
    <script>
        function onSuccess(xhttp){
            showModal("Success", "File <?= $f ?>.ini updated!");
            console.log(xhttp.status, xhttp.statusText, xhttp.responseText);
        }

        function onError(xhttp){
            showModal("Error", "Error updating file <?= $f ?>.ini. See console for details...");
            console.log(xhttp.status, xhttp.statusText, xhttp.responseText);
        }

        function saveConfig(){
            var body = {};
            body['file_name'] = "<?= $f ?>";
            body['contents'] = document.getElementById("swat-ini-file").textContent;

            var username = "<?= $basic_auth_config->get_val('basic_auth_username') ?>";
            var password = "<?= $basic_auth_config->get_val('basic_auth_password') ?>";
            var endpoint = "<?= $main_config->get_val('base_url') ?>/api/config/set";

            callEndpoint(endpoint, body, username, password, onSuccess, onError);
        }
    </script>
