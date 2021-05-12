<?php
    namespace app\session_utils;

    include_once "./app/classes/user_access_levels.php";
    include_once "./defs/swat.php";

    function start_session(){
        if (session_status() === PHP_SESSION_NONE) {
            session_save_path(DOCUMENT_ROOT.'\temp');
            session_start();
        }
    }

    function end_session(){
        session_unset();
        session_destroy();
    }

    function is_user_logged_in(int $user_access_level){
        $active = (isset($_SESSION["active"]) && $_SESSION["active"] === true);
        $remember_me = (isset($_SESSION["remember_me"]) && $_SESSION["remember_me"] === true);
        $time = isset($_SESSION["last_logged_in"]) ? $_SESSION["last_logged_in"] : -1;
        $ual = isset($_SESSION["user_access_level"]) ? $_SESSION["user_access_level"] : -1;
        $now = time();

        if(!$remember_me){
            $_SESSION["active"] = false;
        }

        return $active && ($time > ($now - (24 * 60 * 60))) && ($user_access_level >= $ual);
    }

    function log_user_in(int $user_access_level, bool $remember_me){
        $_SESSION["active"] = true;
        $_SESSION["last_logged_in"] = time();
        $_SESSION["user_access_level"] = $user_access_level;
        $_SESSION["remember_me"] = $remember_me;
    }
