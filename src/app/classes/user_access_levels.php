<?php
    namespace app\user_access_levels;

    class user_access_level{
        const user = 0;
        const super_user = 1;
        const admin = 2;
        const system = 3;
        const dev = 4;

        private $user_access_level;

        public function __construct(int $user_access_level){
            $this->user_access_level = $user_access_level;
        }

        public function __destruct(){

        }

        public function to_string(){
            switch($this->user_access_level){
                case user_access_level::user:
                    return 'user';
                case user_access_level::super_user:
                    return 'super user';
                case user_access_level::admin:
                    return 'admin';
                case user_access_level::system:
                    return 'system';
                case user_access_level::dev:
                    return 'dev';
                default:
                    return 'unknown';
            }
        }
    }
