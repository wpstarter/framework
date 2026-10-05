<?php
/**
 * Fill WP_User class if missing for WordPress
 */
if(!class_exists(WP_User::class)){
    class WP_User{
        public $data;
        public function __construct($id = 0, $name = '', $site_id = 0)
        {
            $this->data = new stdClass();
        }
    }
}
