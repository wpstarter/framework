<?php

namespace WpStarter\Wordpress\Plugins;

use WpStarter\Support\Collection;
use WpStarter\Support\Str;


class Updater
{
    protected $pluginsRepository;
    protected $pluginsDir;
    public function __construct($pluginsDir)
    {
        $this->pluginsDir=rtrim(str_replace('\\', '/', $pluginsDir), '/');;
        $this->pluginsRepository=new Repository($this->pluginsDir);
    }

    /**
     * @return Collection|Plugin[]
     */
    function getUpdates(){
        $plugins=$this->pluginsRepository->getPlugins();
        $active=[];
        $to_send = compact( 'plugins', 'active' );
        $data       = array(
            'plugins'      => json_encode( $to_send ),
            'translations' => json_encode( [] ),
            'locale'       => json_encode( [] ),
            'all'          => json_encode( true ),
        );
        $formData=http_build_query($data);
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($formData),
            'User-Agent: WordPress/'.$this->wordpressVersion()  . '; ' . 'http://localhost'
        );
        $token = ws_config('wordpress.plugins.updater.token');
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        $options = array(
            'http' => array(
                'header'  => implode("\r\n", $headers),
                'method'  => 'POST',
                'content' => $formData,
                'timeout' => ws_config('wordpress.plugins.updater.timeout', 60),
            ),
        );

        $context = stream_context_create($options);

        $response = $this->request(
            ws_config('wordpress.plugins.updater.api_url','https://api.wordpress.org/plugins/update-check/1.1/'),
            $context
        );
        if ($response === false) {
            throw new \RuntimeException(
                'Failed to connect to WordPress update API.'
            );
        }

        $update = json_decode($response,true);

        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_array($update)
            || !isset($update['plugins'])
            || !is_array($update['plugins'])
        ) {
            throw new \RuntimeException(
                'Invalid response from WordPress update API. '.Str::limit($response)
            );
        }
        foreach ($update['plugins'] as $slug=>$info){
            if(isset($plugins[$slug])){
                $plugins[$slug]->NewVersion=$info['new_version'];
                $plugins[$slug]->UpdatePackage=$info['package'];
            }
        }
        return $plugins;
    }

    protected function request($url, $context)
    {
        return @file_get_contents($url, false, $context);
    }

    protected function wordpressVersion()
    {
        $file = defined('ABSPATH') && defined('WPINC')
            ? ABSPATH.WPINC.'/version.php'
            : dirname($this->pluginsDir, 2).'/wp-includes/version.php';
        $contents = is_file($file) && is_readable($file) ? file_get_contents($file) : '';

        // Never include WordPress files, even when inspecting a local installation.
        return preg_match('/\$wp_version\s*=\s*([\'"])([^\'"\r\n]+)\1\s*;/', $contents, $match)
            ? $match[2]
            : '6.x';
    }


}
