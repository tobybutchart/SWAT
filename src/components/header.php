<?php
    include_once "./app/utilities/page_utils.php";
?>
<!doctype html>
<html lang="en" class="h-swat">
    <head>
<?= app\page_utils\head_get_contents(); ?>
    </head>
    <body class="h-swat">
        <main class="h-swat">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark navbar-swat" aria-label="swat-navbar">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/"><img src="/web/img/swat-76x30.png" alt="SWAT"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#swat-navbar" aria-controls="swat-navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="swat-navbar">
                        <ul class="navbar-nav me-auto mb-2 mb-md-0">
                            <li class="nav-item">
                                <a class="nav-link <?= app\page_utils\nav_get_class(""); ?>" aria-current="page" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://github.com/tobybutchart/SWAT/wiki">Guide</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= app\page_utils\nav_get_class("config"); ?>" href="/Config">Config</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= app\page_utils\nav_get_class("logs"); ?>" href="/Logs">Logs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://github.com/tobybutchart/SWAT">View on Github</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Nested">Nested</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link navbar-swat-logout <?= app\page_utils\nav_get_class("logout"); ?>" href="/Logout">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Button trigger modal - hidden as I am a disgusting human being -->
            <button type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="btn-staticBackdrop" style="display: none"></button>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="staticBackdropBody">Stuff n that</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
