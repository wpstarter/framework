<?php

namespace WpStarter\Wordpress\Console\Commands\Database;

use WpStarter\Database\Console\WipeCommand;
use WpStarter\Database\Migrations\Migrator;
class MigrationWipeCommand extends WipeCommand
{
    protected $migrator;
    protected $app;
    public function __construct(Migrator $app)
    {
        $this->migrator=$app;
        parent::__construct();
    }

    protected function dropAllTables($database)
    {
        $files=$this->migrator->getMigrationFiles($this->getMigrationPaths());
        $tables=[];
        foreach ($files as $file){
            $content=file_get_contents($file);
            if(preg_match_all('#create\s*\(\s*["\']([^"\']+)["\']#is',$content,$matches)){
                $tables=array_merge($tables,$matches[1]);
            }
        }
        foreach ($tables as $table) {
            $this->laravel['db']->connection($database)
                ->getSchemaBuilder()->dropIfExists($table);
        }
        $this->migrator->deleteRepository();
    }

    protected function getMigrationPaths()
    {
        // Here, we will check to see if a path option has been defined. If it has we will
        // use the path relative to the root of the installation folder so our database
        // migrations may be run for any customized path from within the application.
        if ($this->input->hasOption('path') && $this->option('path')) {
            return ws_collect($this->option('path'))->map(function ($path) {
                return ! $this->usingRealPath()
                    ? $this->laravel->basePath().'/'.$path
                    : $path;
            })->all();
        }

        return array_merge(
            $this->migrator->paths(), [$this->getMigrationPath()]
        );
    }
    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations';
    }
}