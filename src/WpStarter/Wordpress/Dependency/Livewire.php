<?php

namespace WpStarter\Wordpress\Dependency;

class Livewire
{
    protected $done;
    protected $styleOptions=[];
    protected $scriptOptions=[];
    function enqueue($styleOptions=[],$scriptOptions=[]){
        if(function_exists('add_action') && class_exists(\Livewire\Livewire::class) && !$this->done) {
            $this->styleOptions=$styleOptions;
            $this->scriptOptions=$scriptOptions;
            add_action('wp_print_styles', [$this,'outputStyles'], 11);//After other styles
            add_action('wp_print_footer_scripts', [$this,'outputScripts'], 9);//before other scripts
            $this->done=true;
        }
        return $this->done;
    }
    function outputStyles(){
        echo \Livewire\Livewire::styles();
    }
    function outputScripts(){
        echo \Livewire\Livewire::scripts();
    }
}
