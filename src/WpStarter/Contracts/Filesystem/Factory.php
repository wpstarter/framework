<?php

namespace WpStarter\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \WpStarter\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
