<?php
    namespace app\session_utils;

    include_once "./app/classes/user_access_levels.php";
    include_once "./defs/swat.php";

/**
 *  starts user session - used in main_controller
 *
 *  @return    void
 */
    function start_session(){
        if (session_status() === PHP_SESSION_NONE) {
            session_save_path(DOCUMENT_ROOT.'\temp');
            session_start();
        }
    }

/**
 *  ends user session - used in logout routines
 *
 *  @return    void
 */
    function end_session(){
        session_unset();
        session_destroy();
    }

/**
 *  checks whether user is logged in
 *
 *  @param     int        $user_access_level    defines access level (TODO)
 *  @return    boolean                          true if active login set
 */
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

/**
 *  logs in a user
 *
 *  @param     int       $user_access_level    defines access level (TODO)
 *  @param     bool      $remember_me          defines whether to remember user (TODO)
 *  @return    void
 */
    function log_user_in(int $user_access_level, bool $remember_me){
        $_SESSION["active"] = true;
        $_SESSION["last_logged_in"] = time();
        $_SESSION["user_access_level"] = $user_access_level;
        $_SESSION["remember_me"] = $remember_me;
    }
