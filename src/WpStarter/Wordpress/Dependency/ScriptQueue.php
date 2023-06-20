<?php

namespace WpStarter\Wordpress\Dependency;

class ScriptQueue
{
    public static $booted=false;
    function boot(){
        if(static::$booted){
            return ;
        }
        static::$booted=true;
        add_action('wp_print_scripts',[$this,'headerScript'],9);
        add_action('wp_print_footer_scripts', [$this,'footerScript'], 100);

        add_action('admin_print_scripts',[$this,'headerScript'],9);
        add_action('admin_print_footer_scripts',[$this,'footerScript'], 100);
    }
    function headerScript(){
        echo '<script>window.wpstarter=[];</script>';
    }
    function footerScript(){
        echo <<<FOOTER
<script>
    window.wpstarter.forEach(function(f){f();});
    window.wpstarter.push=function(f){f();}
</script>
FOOTER;
    }
}
