<?php
/**
 * Fill WP_User class if missing for WordPress
 */
if(!class_exists(WP_User::class)){
    class WP_User{
        public function __construct()
        {
        }
    }
}
