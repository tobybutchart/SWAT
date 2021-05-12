<?php
    namespace app\endpoint;

    include_once "./app/classes/http_response_codes.php";
    include_once "./app/utilities/json_utils.php";

    class endpoint{

        private $body = [];
        private $response = [];
        private $username;
        private $password;
        private $url;
        private $http_response_code;

        private function clear_output(){
            $this->response = [];
            $this->http_response_code->set_response_code(0);
        }

        public function __construct(){
            $this->http_response_code = new \app\http_response_codes\http_response_code(0);
            $this->clear_output();
        }

        public function __destruct(){

        }

        public function set_username(string $username){
            $this->username = $username;
        }

        public function set_password(string $password){
            $this->password = $password;
        }

        public function set_url(string $url){
            $this->url = $url;
        }

        public function set_body(array $body){
            $this->body = $body;
        }

        public function get_val(string $key){
            return array_key_exists($key , $this->response) ? $this->response[$key] : null;
        }

        public function execute(){
            $curl = curl_init();

            if ($curl === false) {
                throw new \Exception('failed to initialize cURL');
            }

            $json_data = json_encode($this->body);

            curl_setopt($curl, CURLOPT_POST, count($this->body));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $this->username.':'.$this->password);
            curl_setopt($curl, CURLOPT_URL, $this->url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            $this->response = json_decode(curl_exec($curl), true);
            $this->http_response_code->set_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));

            if($this->http_response_code->http_response_code !== \app\http_response_codes\http_response_code::ok){
                throw new \Exception("HTTP status: {$this->http_response_code->http_response_code} : {$this->http_response_code->to_string()}");
            }

            if ($this->response === false) {
                throw new \Exception('cURL Exception: '.curl_error($curl).': '.curl_errno($curl));
            }

            if (empty($this->response)) {
                throw new \Exception('JSON Exception: '.\app\json_utils\get_last_json_error().': '.json_last_error());
            }

            curl_close($curl);
        }

        public function response_to_string(){
            return json_encode($this->response);
        }

        public function print_response(){
            print_r($this->response_to_string());
        }
    }
