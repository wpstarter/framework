<?php

namespace WpStarter\Wordpress\Services;

use WpStarter\Contracts\Foundation\Application;

class QueryMonitor
{
    protected $basePath;
    public function __construct(Application $app)
    {
        $this->basePath=$app->basePath();
    }
    public function boot(){
        add_filter('qm/component_dirs',function($dir){
            $dir['wpstarter']=$this->basePath;
            return $dir;
        });
        add_filter('qm/component_name/wpstarter',function($type){
            return 'WpStarter';
        });
    }
}
