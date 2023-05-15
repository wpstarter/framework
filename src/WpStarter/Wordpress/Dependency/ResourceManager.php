<?php

namespace WpStarter\Wordpress\Dependency;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Support\Arr;


/**
 * Class ResourceManager
 * @method $this addJs($handle = null, $src = false, $deps = array(), $ver = false, $in_footer = true, $data = array())
 * @method $this addCss($handle = null, $src = false, $deps = array(), $ver = false, $media = 'all')
 * @method $this addAdminJs($handle = null, $src = false, $deps = array(), $ver = false, $in_footer = true, $data = array())
 * @method $this addAdminCss($handle = null, $src = false, $deps = array(), $ver = false, $media = 'all')
 * @method $this registerJs($handle = null, $src = false, $deps = array(), $ver = false, $in_footer = true, $data = array())
 * @method $this registerCss($handle = null, $src = false, $deps = array(), $ver = false, $media = 'all')
 */
class ResourceManager
{
    var $resources = array();
    var $did = array();
    var $last = array();
    var $js_translations = [];
    var $typeAlias = array(
        'css' => 'c',
        'js' => 'j',
        'admincss' => 'ac',
        'adminjs' => 'aj',
        'acss' => 'ac',
        'ajs' => 'aj',
        'registerjs' => 'rj',
        'registercss' => 'rc',
        'rjs' => 'rj',
        'rcss' => 'rc',
    );
    protected $app;
    protected $defaultVersion;

    /**
     * @param Application $application
     */
    function __construct(Application $application)
    {
        $this->app = $application;
        foreach (array_unique($this->typeAlias) as $type) {
            $this->resources[$type] = array();
            $this->did[$type] = false;
        }
    }

    /**
     * @param $handles
     * @return $this
     */
    function addVendor($handles)
    {
        $handles = is_array($handles) ? $handles : func_get_args();
        foreach ($handles as $handle) {
            $this->addJs($handle);
            $this->addCss($handle);
        }
        return $this;
    }

    function addAdminVendor($handle)
    {
        $this->addAdminJs($handle);
        $this->addAdminCss($handle);
        return $this;
    }

    function boot()
    {
        $this->loadResources();
        add_action('init', array($this, 'ensureWpScriptStyle'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueCssJs'), 100);
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminCssJs'), 100);
    }

    function ensureWpScriptStyle()
    {
        wp_enqueue_script(false);
        wp_enqueue_style(false);
        $this->register();
        do_action('ws_register_scripts', $this);
    }


    function loadResources()
    {
        $resources = $this->app['config']['resources'];
        $this->defaultVersion = $resources['default_version']
            ?? defined('WS_ASSETS_VERSION') ? WS_ASSETS_VERSION : ( defined('WS_VERSION') ? WS_VERSION : '' );
        unset($resources['default_version']);
        if ($resources) {
            foreach ($resources as $resource) {
                $this->loadResource($resource);
            }
        }
    }

    function loadResource($resource)
    {
        foreach ($resource as $type => $res) {
            $type = $this->typeAlias($type);
            if (is_array($this->resources[$type]) && is_array($res)) {
                $this->resources[$type] = array_merge($this->resources[$type], $res);
            }
        }
    }

    protected function typeAlias($type)
    {
        $type = strtolower($type);
        if (isset($this->typeAlias[$type])) {
            return $this->typeAlias[$type];
        }
        return $type;
    }

    function addResource($file)
    {

        $file_name = basename($file);

        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        if ($file_extension == 'css') {
            $this->addCss(array($file_name, $file));
        }
        if ($file_extension == 'js') {
            $this->addJs(array($file_name, $file));
        }
        return $this;
    }

    function __call($method, $args)
    {
        if (!$args) {
            return $this;
        }
        $data = (isset($args[0]) && is_array($args[0])) ? $args[0] : $args;
        if (!$data) {
            $data = $this->last;
        }
        if (strpos($method, 'add') === 0) {
            $type = substr($method, 3);
            $this->add($type, $data);
        }
        if (strpos($method, 'register') === 0) {
            $type = $method;
            $this->add($type, $data);
        }
        $this->last = $data;
        return $this;
    }

    protected function register()
    {
        $this->did['rc'] = $this->did['rj'] = true;
        if (isset($this->resources['rc']) && is_array($this->resources['rc'])) {
            foreach ($this->resources['rc'] as $css) {
                $this->_registerCss($css);
            }
        }
        if (isset($this->resources['rj']) && is_array($this->resources['rj'])) {
            foreach ($this->resources['rj'] as $js) {
                $this->_registerJs($js);
            }
        }
    }

    protected function _registerJs($js)
    {
        $js = array_pad($js, 6, null);
        @list($handle, $src, $deps, $ver, $in_footer, $data) = $js;
        $src = ws_asset($src);
        $deps = Arr::wrap($deps);
        empty($ver) && $ver = $this->defaultVersion;
        wp_register_script($handle, $src, $deps, $ver, $in_footer);
        $this->setupTranslation($handle);
        if ($data) {
            foreach ($data as $objName => $values) {
                if (is_string($objName) && $values) {
                    if ($values instanceof \Closure) {
                        $values = $values();
                    }
                    wp_localize_script($handle, $objName, $values);
                }
            }
        }
    }

    protected function _registerCss($css)
    {
        $css = array_pad($css, 5, null);
        @list($handle, $src, $deps, $ver, $media) = $css;
        $src = ws_asset($src);
        $deps = Arr::wrap($deps);
        empty($ver) && $ver = $this->defaultVersion;
        isset($media) || $media = 'all';
        wp_register_style($handle, $src, $deps, $ver, $media);
    }

    protected function add($type, $data)
    {
        $type = $this->typeAlias($type);
        if (count($data) == 1 && strpos($data[0], '/')) {
            $data[1] = $data[0];
            $data[0] = 'imr-' . md5($data[1]);
        }
        if ($this->did[$type]) {
            if (strpos($type, 'j') !== false) {
                if ($type == 'rj') {
                    $this->_registerJs($data);
                } else {
                    $this->enqueue_js(array($data));
                }
            } else {
                if ($type == 'rc') {
                    $this->_registerCss($data);
                } else {
                    $this->enqueue_css(array($data));
                }
            }
        } else {
            if (is_array($this->resources[$type])) {
                $this->resources[$type][] = $data;
            }
        }
    }

    function enqueueAdminCssJs()
    {
        $this->did['ac'] = $this->did['aj'] = true;
        $this->enqueue_css($this->resources['ac']);
        $this->enqueue_js($this->resources['aj']);
    }


    /**
     * @use \WP_Scripts $wp_scripts
     */
    function enqueueCssJs()
    {
        $this->did['c'] = $this->did['j'] = true;
        $this->enqueue_css($this->resources['c']);
        $this->enqueue_js($this->resources['j']);
    }

    function enqueue_css($css_queue)
    {
        global $wp_styles;
        !is_array($css_queue) && $css_queue = array();
        foreach ($css_queue as $css) {
            @list($handle, $src, $deps, $ver, $media) = array_pad((array)$css, 5, null);

            isset($src) || $src = false;
            $deps = $deps ? (array)$deps : [];
            empty($ver) && $ver = $this->defaultVersion;
            isset($media) || $media = 'all';
            $src = ws_asset($src);

            wp_enqueue_style($handle, $src, $deps, $ver, $media);
        }
    }

    function enqueue_js($js_queue)
    {
        !is_array($js_queue) && $js_queue = array();
        foreach ($js_queue as $js) {
            @list($handle, $src, $deps, $ver, $in_footer, $data) = array_pad((array)$js, 6, null);
            isset($src) || $src = false;
            $deps = $deps ? (array)$deps : [];
            empty($ver) && $ver = $this->defaultVersion;
            isset($in_footer) || $in_footer = true;
            $src = ws_asset($src);
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
            $this->setupTranslation($handle);
            if ($data) {
                foreach ($data as $objName => $values) {
                    if (is_string($objName) && $values) {
                        if ($values instanceof \Closure) {
                            $values = $values();
                        }
                        wp_localize_script($handle, $objName, $values);
                    }
                }
            }
        }
    }

    public function getTranslationData($domain)
    {
        $translations = get_translations_for_domain($domain);

        $locale = array(
            '' => array(
                'domain' => $domain,
                'lang' => determine_locale(),
            ),
        );

        if (!empty($translations->headers['Plural-Forms'])) {
            $locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
        }

        foreach ($translations->entries as $msgid => $entry) {
            $locale[$msgid] = $entry->translations;
        }

        return $locale;
    }

    public function translateUsing($domain)
    {
        if ($this->last) {
            $handle = $this->last[0] ?? null;
            if ($handle) {
                $this->setTranslation($handle, $domain);
            }
        }
        return $this;

    }

    public function setTranslation($handle, $domain)
    {
        $this->js_translations[$handle] = $domain;
        return $this;
    }

    protected function setupTranslation($handle)
    {
        $domain = $this->js_translations[$handle] ?? null;
        if (!$domain) {
            return;
        }
        $locale = $this->getTranslationData($domain);
        $content = 'wp.i18n.setLocaleData( ' . json_encode($locale) . ', "' . $domain . '" );';
        wp_script_add_data($handle, 'data', $content);
        unset($this->js_translations[$handle]);
    }

    function __invoke()
    {
        return $this;
    }

}
