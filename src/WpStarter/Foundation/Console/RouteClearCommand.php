<?php

namespace WpStarter\Foundation\Console;

use WpStarter\Console\Command;
use WpStarter\Filesystem\Filesystem;

class RouteClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the route cache file';

    /**
     * The filesystem instance.
     *
     * @var \WpStarter\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \WpStarter\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->getCachedRoutesPath());

        $this->info('Route cache cleared!');
    }
}
