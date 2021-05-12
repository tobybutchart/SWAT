<?php
    namespace app\api_logging;

    include_once "./app/classes/log.php";
    include_once "./defs/swat.php";

    class api_log_type {
        const __default = self::unknown;

        const unknown = -1;
        const request = 0;
        const response = 1;
        const error = 2;

        private $api_log_type;

        public function __construct(int $api_log_type){
            $this->api_log_type = $api_log_type;
        }

        public function __destruct(){

        }

        public function to_string(){
            switch($this->api_log_type){
                case api_log_type::request:
                    return 'request';
                case api_log_type::response:
                    return 'response';
                case api_log_type::error:
                    return 'error';
                default:
                    return 'unknown';
            }
        }
    }

    class api_log extends \app\logging\log{

        private $guid;
        private $contents;
        private $http_headers_only;

        public function __construct(){
            parent::__construct(DOCUMENT_ROOT.'\logs', "api", \app\logging\log_level::all, false);
            $this->guid = $this->set_guid();
            $this->set_http_headers_only(true);
        }

        public function __destruct(){

        }

        public function set_http_headers_only(bool $bool){
            $this->http_headers_only = $bool;
        }

        private function set_guid(){
            mt_srand((double)microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $guid = substr($charid, 0, 8).$hyphen
                   .substr($charid, 8, 4).$hyphen
                   .substr($charid, 12, 4).$hyphen
                   .substr($charid, 16, 4).$hyphen
                   .substr($charid, 20, 12);

            return $guid;
        }

        public function get_guid(){
            return $this->guid;
        }

        private function get_header_list(int $api_log_type) {
            $headerList = [];

            if($api_log_type == api_log_type::request){
                foreach ($_SERVER as $name => $value) {
                    if ((!$this->http_headers_only) || (preg_match('/^HTTP_/',$name))) {
                        // convert HTTP_HEADER_NAME to Header-Name
                        $name = strtr(substr($name,5),'_',' ');
                        $name = ucwords(strtolower($name));
                        $name = strtr($name,' ','-');
                        // add to list
                        $headerList[$name] = $value;
                    }
                }
            }else{
                $arr = headers_list();
                foreach($arr as $header){
                    list($key, $value) = explode(':', $header, 2);
                    $headerList[trim($key)] = trim($value);
                }
            }

            return $headerList;
        }

        private function set_body(int $api_log_type, int $response_code = 0, string $response = '', \Exception $e = null){
            $this->contents = sprintf(
                "%s %s %s\n\nHTTP headers:\n",
                filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING),
                filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_STRING),
                filter_input(INPUT_SERVER, "SERVER_PROTOCOL", FILTER_SANITIZE_STRING)
            );

            foreach ($this->get_header_list($api_log_type) as $name => $value) {
                $this->contents .= $name.': '.$value."\n";
            }

            if($api_log_type == api_log_type::request){
                $this->contents .= "\nRequest body:\n";
                $this->contents .= file_get_contents('php://input')."\n";
            }elseif($api_log_type == api_log_type::response){
                $this->contents .= "\nResponse Code:\n";
                $this->contents .= $response_code."\n";
                $this->contents .= "\nResponse:\n";
                $this->contents .= $response."\n";
            }elseif($api_log_type == api_log_type::error){
                $this->contents .= "\nResponse Code:\n";
                $this->contents .= $response_code."\n";
                $this->contents .= "\nResponse:\n";
                $this->contents .= $response."\n";
                $this->contents .= "\nError:\n";
                $this->contents .= sprintf(
                    "Message: %s\nCode: %c\nFile: %s\nLine: %c\n",
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getFile(),
                    $e->getLine()
                );
            }
        }

        private function internal_log(){
            $this->log($this->contents, -1);
        }

        public function log_request() {
            $this->set_file_prefix("api-request-".$this->guid);
            $this->set_body(api_log_type::request);
            $this->internal_log();
        }

        public function log_response(int $response_code, string $response) {
            $this->set_file_prefix("api-response-".$this->guid);
            $this->set_body(api_log_type::response, $response_code, $response);
            $this->internal_log();
        }

        public function log_error(int $response_code, string $response, \Exception $e) {
            $this->set_file_prefix("api-error-".$this->guid);
            $this->set_body(api_log_type::error, $response_code, $response, $e);
            $this->internal_log();
        }
    }
