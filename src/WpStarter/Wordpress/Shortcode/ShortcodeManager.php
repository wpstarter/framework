<?php

namespace WpStarter\Wordpress\Shortcode;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Wordpress\Contracts\Shortcode;
use WpStarter\Wordpress\View\Shortcode as ShortcodeView;
class ShortcodeManager
{
    protected $shortcodes = [];
    protected $app;
    protected $boot_hook = ['template_redirect', 10];
    protected $booted = false;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function setBootHook($hook, $priority = 10)
    {
        $this->boot_hook = [$hook, $priority];
        return $this;
    }

    public function boot()
    {
        if (!$this->booted) {
            add_action($this->boot_hook[0], [$this, 'bootShortcodes'], $this->boot_hook[1]);
        }
        return $this;
    }

    public function bootShortcodes()
    {
        if (is_singular()) {
            if ($post = get_post()) {
                foreach ($this->shortcodes as $tag => $shortcode) {
                    if (has_shortcode($post->post_content, $tag)) {
                        if(method_exists($shortcode,'boot')) {
                            $this->app->call([$shortcode, 'boot']);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param string|Shortcode $shortcode
     * @param $callable
     * @return $this
     */
    function add($shortcode, $callable = null)
    {
        if(!function_exists('add_shortcode')){
            return $this;
        }
        if(is_string($shortcode) && class_exists($shortcode)){
            $shortcode = $this->app->make($shortcode);
        }
        if ($shortcode instanceof Shortcode) {
            $tag = $shortcode->getTag();
            if($callable && is_string($callable)){
                $tag=$callable;
            }
            $this->shortcodes[$tag] = $shortcode;
            add_shortcode($tag, function ($attributes, $content = '') use ($shortcode) {
                if($shortcode instanceof ShortcodeView) {
                    $shortcode->setAttributes($attributes);
                    $shortcode->setContent($content);
                    if(method_exists($shortcode,'mount')) {
                        $this->app->call([$shortcode, 'mount']);
                    }
                    $result = $shortcode->render();
                    $shortcode->cleanup();
                }else{
                    $result = $shortcode->render($attributes,$content);
                }

                return $result;
            });
        } else {
            if($callable) {
                add_shortcode($shortcode, function ($attributes, $content = '') use ($callable) {
                    return $this->app->call($callable, [$attributes, $content], 'render');
                });
            }
        }
        return $this;
    }

    public function get($tag)
    {
        return $this->shortcodes[$tag] ?? null;
    }

    public function has($tag)
    {
        return isset($this->shortcodes[$tag]);
    }
}