<?php
use WpStarter\Wordpress\Http\Response\Content;
use WpStarter\Wordpress\Http\Response\Shortcode;
use WpStarter\Wordpress\Http\Response\Page;
if (! function_exists('wp_view')) {
    /**
     * Get the full page view
     *
     * @param  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \WpStarter\Wordpress\Http\Response\Page
     */
    function wp_view($view, $data = [], $mergeData = [])
    {
        return Page::make($view, $data, $mergeData);
    }
}
if (! function_exists('content_view')) {
    /**
     * Get the post content view
     *
     * @param  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \WpStarter\Wordpress\Http\Response\Content
     */
    function content_view($view, $data = [], $mergeData = [])
    {
        return Content::make($view, $data, $mergeData);
    }
}
if (! function_exists('shortcode_view')) {
    /**
     * Get the shortcode view
     *
     * @param  string  $tag
     * @param  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \WpStarter\Wordpress\Http\Response\Shortcode
     */
    function shortcode_view($tag, $view, $data = [], $mergeData = [])
    {
        return Shortcode::make($tag, $view, $data, $mergeData);
    }
}

if (! function_exists('ws_plugin_url')) {
    /**
     * Get the shortcode view
     *
     */
    function ws_plugin_url($path = '', $scheme = null)
    {
        $basePath=str_replace(ABSPATH,'',__WS_FILE__);
        $basePath=trim(dirname($basePath),'\/');
        return site_url($basePath.'/'.ltrim($path,'/'),$scheme);
    }
}
