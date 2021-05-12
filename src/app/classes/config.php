<?php
    namespace app\config;

    class config{
        const _GLOBAL = 'global';
        const _BASE   = './config/';
        const _EXT    = '.ini';

        private $file;
        private $override;
        private $config;
        private $global;

        public function __construct(string $file, string $section){
            $this->file = $this::_BASE.$file.$this::_EXT;
            $this->config = file_exists($this->file) ? parse_ini_file($this->file, true): [];
            $this->global = $this->get_section_array($this::_GLOBAL);
            $this->override = $this->get_section_array($section);
        }

        public function __destruct(){

        }

        private function get_section_array(string $section){
            return array_key_exists($section, $this->config) ? $this->config[$section] : [];
        }

        public function get_val(string $key, $default = ''){
            for ($i = 0; $i <= 1; $i++) {
                switch ($i) {
                    case 0:
                        $a = $this->override;
                        break;
                    case 1:
                        $a = $this->global;
                        break;
                    default:
                        $a = [];
                }

                if (array_key_exists($key, $a) && !empty($a[$key])){
                    return $a[$key];
                }
            }

            return $default;
        }

        public function get_array(string $key){
            $a = array_key_exists($key, $this->global) ? $this->global[$key] : [];
            array_merge($a, array_key_exists($key, $this->override) ? $this->override[$key] : []);

            return $a;
        }

        public function set_contents(string $contents){
            $return = false;

            if ($file = fopen($this->file, 'w')){
                $start_time = microtime(TRUE);

                do{
                    $can_write = flock($file, LOCK_EX);
                    // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                    if(!$can_write){
                        usleep(round(rand(0, 100) * 1000));
                    }
                }while((!$can_write)and((microtime(TRUE) - $start_time) < 5));

                //file was locked so now we can store information
                if ($can_write){
                    fwrite($file, $contents);
                    flock($file, LOCK_UN);
                    $return = true;
                }

                fclose($file);
            }

            return $return;
        }
    }
