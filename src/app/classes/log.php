<?php
    namespace app\logging;

    abstract class log_type{
        const __default = self::info;

        const unknown = -1;
        const debug = 0;
        const info  = 1;
        const error = 2;
        const fatal = 3;
    }

    abstract class log_level{
        const __default = self::errors_only;

        const all              = 0;
        const info_and_errors  = 1;
        const errors_only      = 2;
    }

    class log{
        const _PERMISSIONS  = 0777;
        const _EXT          = '.log';

        private $debug;
        private $path;
        private $file;
        private $log_level;
        private $date_in_file_name;

        public $has_errors;
        public $errors = [];

        public function __construct(string $path, string $file_prefix, int $log_level, bool $date_in_file_name, bool $debug = false){
            $this->has_errors = false;
            $this->date_in_file_name = $date_in_file_name;

            $this->set_debug($debug);
            $this->set_log_level($log_level);
            $this->set_path($path);
            $this->set_file($file_prefix, $date_in_file_name);

            if($this->debug){
                $this->log('Log started', log_type::debug);
                var_dump($this->path);
                var_dump($this->file);
            }
        }

        public function __destruct(){
            if($this->debug){
                $this->log('Log ended', log_type::debug);
            }
        }

        private function log_type_to_str(int $log_type){
            switch($log_type){
                case log_type::debug:
                    return '[DEB]';
                case log_type::info:
                    return '[INF]';
                case log_type::error:
                    return '[ERR]';
                case log_type::fatal:
                    return '[FAT]';
                default:
                    return '[UNK]';
            }
        }

        private function get_path(){
            return $this->path;
        }

        private function set_path(string $path){
            $this->path = $path.$this->date_to_str("\\", false);
        }

        private function get_file(){
            return $this->file;
        }

        private function set_file(string $file_prefix, bool $date_in_file_name){
            $s = '';

            if($date_in_file_name){
                $s = $this->date_to_str("-", true);
            }

            $this->file = $this->path.'\\'.$file_prefix.$s.$this::_EXT;
        }

        private function get_log_level(){
            return $this->log_level;
        }

        private function set_log_level(int $log_level){
            $this->log_level = $log_level;
        }

        private function set_debug(bool $debug){
            $this->debug = $debug;
        }

        public function get_errors(){
            var_dump($this->errors);
        }

        private function add_error($e){
            array_push($this->errors, $e);
            $this->has_errors = true;
        }

        private function date_to_str(string $delimeter, bool $use_time){
            $year = $delimeter.date("Y");
            $month = $delimeter.date("m");
            $day = $delimeter.date("d");

            $hour = $use_time ? $delimeter.date("G") : "";
            $min = $use_time ? $delimeter.date("i") : "";
            $sec = $use_time ? $delimeter.date("s") : "";
            $ms = $use_time ? round((microtime(true) - floor(microtime(true))) * 1000000) : "";

            $s = $year.$month.$day.$hour.$min.$sec.$ms;

            return $s;
        }

        private function can_log(int $log_type){
            switch($this->log_level){
                case log_level::info_and_errors:
                    if($log_type === log_type::debug){
                        return false;
                    }
                case log_level::errors_only:
                    if($log_type === log_type::debug || $log_type === log_type::info){
                        return false;
                    }
                default: return true;
            }

            return true;
        }

        public function set_file_prefix(string $file_prefix){
            $this->set_file($file_prefix, $this->date_in_file_name);
        }

        public function log(string $string_to_log, int $log_type){
            if($this->can_log($log_type)){
                if(!file_exists($this->path) && !realpath(($this->file)) && !mkdir($this->path, $this::_PERMISSIONS, true)){
                    $this->add_error("$this->path does not exist and cannot be created.");
                    return false;
                }

                try{
                    $file = fopen($this->file, "a");

                    $d = ($log_type > log_type::unknown) ? "[".date("d/m/Y H:i:s")."]" : "";
                    $l = ($log_type > log_type::unknown) ? $this->log_type_to_Str($log_type) : "";
                    $n = ($log_type > log_type::unknown) ? "[$string_to_log]" : $string_to_log;

                    $s = $d.$l.$n.PHP_EOL;

                    fwrite($file, $s);
                    fclose($file);
                } catch (Exception $e){
                    $this->add_error($e->getMessage());
                }
            }
        }
    }
