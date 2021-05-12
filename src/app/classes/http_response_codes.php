<?php
    namespace app\http_response_codes;

    class http_response_code{
        const __default = self::unknown;

        const unknown = 0;
        const ok = 200;
        const bad_request = 400;
        const unauthorized = 401;
        const not_found = 404;
        const method_not_allowed = 405;
        const not_acceptable = 406;
        const unsupported_media_type = 415;
        const internal_error = 500;

        public $http_response_code;

        public function __construct(int $http_response_code){
            $this->set_response_code($http_response_code);
        }

        public function __destruct(){

        }

        public function set_response_code(int $http_response_code){
            $this->http_response_code = $http_response_code;
        }

        public function to_string(){
            switch($this->http_response_code){
                case http_response_code::ok:
                    return 'HTTP/1.1 200 OK';
                case http_response_code::unknown:
                    return 'HTTP/1.1 204 No Content';
                case http_response_code::bad_request:
                    return 'HTTP/1.1 400 Bad Request';
                case http_response_code::unauthorized:
                    return 'HTTP/1.1 401 Unauthorized';
                case http_response_code::not_found:
                    return 'HTTP/1.1 404 Not Found';
                case http_response_code::method_not_allowed:
                    return 'HTTP/1.1 405 Method Not Allowed';
                case http_response_code::not_acceptable:
                    return 'HTTP/1.1 406 Not Acceptable';
                case http_response_code::unsupported_media_type:
                    return 'HTTP/1.1 415 Unsupported Media Type';
                case http_response_code::internal_error:
                    return 'HTTP/1.1 500 Internal Server Error';
            }
        }

        public function set_header(){
            header($this->to_string());
        }
    }
