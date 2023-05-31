<?php

namespace WpStarter\Wordpress\Services;

use WpStarter\Contracts\Foundation\Application;

class QueryMonitor
{
    protected $basePath;
    protected $version;
    public function __construct(Application $app)
    {
        $this->basePath=$app->basePath();
        $this->version=$app->version();
    }
    public function boot(){
        add_filter('qm/component_dirs',function($dir){
            $dir['wpstarter']=$this->basePath;
            return $dir;
        });
        add_filter('qm/component_name/wpstarter',function($type){
            return 'WpStarter';
        });
        add_filter('qm/environment-constants',function($constance){
            $constance=['WpStarter'=>$this->version]+$constance;
            return $constance;
        });
    }
}
