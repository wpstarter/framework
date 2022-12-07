<?php

namespace WpStarter\Wordpress\Contracts;

interface Shortcode
{
    public function getTag();
    public function render();
}