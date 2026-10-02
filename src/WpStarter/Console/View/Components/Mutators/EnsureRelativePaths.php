<?php

namespace WpStarter\Console\View\Components\Mutators;

class EnsureRelativePaths
{
    /**
     * Ensures the given string only contains relative paths.
     *
     * @param  string  $string
     * @return string
     */
    public function __invoke($string)
    {
        if (function_exists('ws_app') && ws_app()->has('path.base')) {
            $string = str_replace(ws_base_path().'/', '', $string);
        }

        return $string;
    }
}
