<?php
    namespace app\json_utils;

/**
 *  returns readable JSON error
 *
 *  @return    string    error message
 */
    function get_last_json_error(){
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No errors';
            break;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                return 'Unknown error';
            break;
        }
    }

/**
 *  returns true if string is valid JSON
 *
 *  @param     string     $string    string to check
 *  @return    boolean               is JSON
 */
    function is_valid_json(string $string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
