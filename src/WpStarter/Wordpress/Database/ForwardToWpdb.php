<?php

namespace WpStarter\Wordpress\Database;
/**
 * @property $last_error
 */
trait ForwardToWpdb
{
    function suppress_errors(...$args){
        return $this->db->suppress_errors(...$args);
    }
    function get_results(...$args){
        return $this->db->get_results(...$args);
    }
}