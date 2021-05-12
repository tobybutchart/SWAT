<?php
    include_once "./app/utilities/page_utils.php";

    $head = app\server_utils\file_with_path("components", "header");
    $foot = app\server_utils\file_with_path("components", "footer");

    include $head;
?>
    <div class="container h-swat swat-log-viewer" >
        <div class="row h-swat">
            <div class="col-sm-12 col-md-2 col-lg-4 h-swat"></div>
            <div class="col-sm-12 col-md-8 col-lg-4 h-swat">
                <form name="input" action="" method="post">
                    <div class="mb-3">
                        <label for="swat-login-username" class="form-label">Username</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="swat-login-username-prepend">@</span>
                            <input type="text" class="form-control" id="swat-login-username" name="swat-login-username" aria-describedby="swat-login-username-prepend" required="">
                            <div class="invalid-feedback">
                                Please enter a username.
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="swat-login-password" class="form-label">Password</label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control" id="swat-login-password" name="swat-login-password" required="">
                            <div class="invalid-feedback">
                                Please enter a password.
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="swat-login-remember-me" name="swat-login-remember-me" value="1">
                        <label class="form-check-label" for="swat-login-remember-me">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-swat btn-md" id="swat-login-btn" name="swat-login-btn">Login</button>
                </form>
            </div>
            <div class="col-sm-12 col-md-2 col-lg-4 h-swat"></div>
        </div>
    </div>
<?php
    include $foot;
