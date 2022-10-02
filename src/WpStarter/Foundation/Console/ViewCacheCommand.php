<?php

namespace WpStarter\Foundation\Console;

use WpStarter\Console\Command;
use WpStarter\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ViewCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Compile all of the application's Blade templates";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('view:clear');

        $this->paths()->each(function ($path) {
            $this->compileViews($this->bladeFilesIn([$path]));
        });

        $this->info('Blade templates cached successfully!');
    }

    /**
     * Compile the given view files.
     *
     * @param  \WpStarter\Support\Collection  $views
     * @return void
     */
    protected function compileViews(Collection $views)
    {
        $compiler = $this->laravel['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $views->map(function (SplFileInfo $file) use ($compiler) {
            $compiler->compile($file->getRealPath());
        });
    }

    /**
     * Get the Blade files in the given path.
     *
     * @param  array  $paths
     * @return \WpStarter\Support\Collection
     */
    protected function bladeFilesIn(array $paths)
    {
        return ws_collect(
            Finder::create()
                ->in($paths)
                ->exclude('vendor')
                ->name('*.blade.php')
                ->files()
        );
    }

    /**
     * Get all of the possible view paths.
     *
     * @return \WpStarter\Support\Collection
     */
    protected function paths()
    {
        $finder = $this->laravel['view']->getFinder();

        return ws_collect($finder->getPaths())->merge(
            ws_collect($finder->getHints())->flatten()
        );
    }
}
