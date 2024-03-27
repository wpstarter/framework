<?php

use WpStarter\Wordpress\Http\Response\Content;
use WpStarter\Wordpress\Http\Response\Shortcode;
use WpStarter\Wordpress\Http\Response\Page;

if (!function_exists('is_wp')) {
    /**
     * Check if we are running inside WordPresss
     * @return bool
     */
    function is_wp()
    {
        return function_exists('wp_check_php_mysql_versions') && defined('ABSPATH');
    }
}
if (!function_exists('wp_view')) {
    /**
     * Get the full page view
     *
     * @param  $view
     * @param \WpStarter\Contracts\Support\Arrayable|array $data
     * @param array $mergeData
     * @return \WpStarter\Wordpress\Http\Response\Page
     */
    function wp_view($view, $data = [], $mergeData = [])
    {
        return Page::make($view, $data, $mergeData);
    }
}
if (!function_exists('content_view')) {
    /**
     * Get the post content view
     *
     * @param  $view
     * @param \WpStarter\Contracts\Support\Arrayable|array $data
     * @param array $mergeData
     * @return \WpStarter\Wordpress\Http\Response\Content
     */
    function content_view($view, $data = [], $mergeData = [])
    {
        return Content::make($view, $data, $mergeData);
    }
}
if (!function_exists('shortcode_view')) {
    /**
     * Get the shortcode view
     *
     * @param  $view
     * @param \WpStarter\Contracts\Support\Arrayable|array $data
     * @param array $mergeData
     * @param string $tag
     * @return \WpStarter\Wordpress\Http\Response\Shortcode
     */
    function shortcode_view($view, $data = [], $mergeData = [], $tag=null)
    {
        return Shortcode::make($view, $data, $mergeData, $tag);
    }
}

if (!function_exists('ws_plugin_url')) {
    /**
     * Get url to ws plugin
     * @param  string      $path   Optional. Path relative to the site URL. Default empty.
     * @param  string|null $scheme Optional. Scheme to give the site URL context. See set_url_scheme().
     * @return string
     */
    function ws_plugin_url($path = '', $scheme = null)
    {
        static $basePath;
        if(!$basePath && defined('ABSPATH') && defined('__WS_FILE__')) {
            $basePath = str_replace(
                str_replace('\\','/',ABSPATH), '',
                str_replace('\\','/',__WS_FILE__));
            $basePath = trim(dirname($basePath), '\/');
        }
        return network_site_url($basePath . '/' . ltrim($path, '/'), $scheme);
    }
}
if (!function_exists('ws_admin_menu')) {
    /**
     * Get current admin menu
     * @return null|\WpStarter\Wordpress\Admin\Routing\Menu
     */
    function ws_admin_menu()
    {
        return ws_app('wp.admin.router')->current();
    }
}

if (!function_exists('ws_admin_url')) {
    /**
     * Get url to admin page
     * @param string $slug The slug or name of the menu
     * @param array $params Query to add to url
     */
    function ws_admin_url($slug=null,$params=[])
    {
        return ws_app('url')->admin($slug,$params);
    }
}
if (!function_exists('ws_pass')) {
    /**
     * Bypass response
     * @return \WpStarter\Wordpress\Http\Response\PassThrough
     */
    function ws_pass()
    {
        return ws_redirect()->pass();
    }
}



if (!function_exists('ws_setting')) {
    /**
     * Get or set the setting
     * @param $key
     * @param $default
     * @return mixed|\WpStarter\Wordpress\Setting\Repository|null
     */
    function ws_setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return ws_app('setting');
        }
        if (is_array($key)) {
            return ws_app('setting')->set($key);
        }

        return ws_app('setting')->get($key, $default);
    }
}

if(!function_exists('ws_admin_notice')){
    /**
     * @param $message
     * @param $type
     * @return \WpStarter\Wordpress\Admin\Notice\NoticeManager|\WpStarter\Wordpress\Admin\Notice\Message
     */
    function ws_admin_notice($message=null,$type='success'){
        if(is_null($message)){
            return ws_app('wp.admin.notice');
        }
        return ws_app('wp.admin.notice')->notify($message,$type);
    }
}

if(!function_exists('ws_enqueue_livewire')){
    /**
     * @param $styleOptions
     * @param $scriptOptions
     * @return boolean
     */
    function ws_enqueue_livewire($styleOptions=[],$scriptOptions=[]){
        return ws_app('wp.livewire')->enqueue($styleOptions,$scriptOptions);
    }
}
if(!function_exists('ws_determine_locale')){
    /**
     * determine locale use wp function or return default locale
     * @param string $default default locale
     * @return string
     */
    function ws_determine_locale($default='en'){
        $locale=$default;
        if(function_exists('determine_locale')){
            $locale=determine_locale();
        }
        return $locale?:$default;
    }
}
if (! function_exists('ws_resources')) {

    /**
     * Get resource manager instance
     * @return \WpStarter\Wordpress\Dependency\ResourceManager
     */
    function ws_resources(){
        return ws_app('resources');
    }
}
if (! function_exists('ws_enqueue_js')) {

    /**
     * Enqueue some javascript.
     *
     * @param string|null $handle
     * @param string|null $src
     * @param string[] $deps
     * @param string|bool $ver
     * @param boolean $in_footer
     * @param array $data
     * @return void
     */
    function ws_enqueue_js($handle = null, $src = false, $deps = [], $ver = false, $in_footer = true, array $data = [])
    {
        ws_resources()->addJs($handle, $src, $deps , $ver, $in_footer, $data);
    }
}

if (! function_exists('ws_enqueue_css')) {

    /**
     * Enqueue some css styles.
     *
     * @param string|null $handle
     * @param string|null $src
     * @param string[] $deps
     * @param string|bool $ver
     * @param string $media
     * @return void
     */
    function ws_enqueue_css($handle = null, $src = false, $deps = array(), $ver = false, $media = 'all')
    {
        ws_resources()->addCss($handle, $src, $deps , $ver, $media);
    }
}
