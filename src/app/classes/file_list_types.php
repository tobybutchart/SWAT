<?php
    namespace app\file_list_types;

    class file_list_type{
        const dirs_only = 0;
        const files_only = 1;

        private $file_list_type;

        public function __construct(int $file_list_type){
            $this->file_list_type = $file_list_type;
        }

        public function __destruct(){

        }

        public function to_string(){
            switch($this->file_list_type){
                case file_list_type::dirs_only:
                    return 'd';
                case file_list_type::files_only:
                    return 'f';
            }
        }
    }
