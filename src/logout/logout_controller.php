<?php
    include_once "./app/utilities/session_utils.php";

    app\session_utils\end_session();
    header("LOCATION: /");
