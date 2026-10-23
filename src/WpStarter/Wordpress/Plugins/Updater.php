<?php

namespace WpStarter\Wordpress\Plugins;

use WpStarter\Support\Collection;


class Updater
{
    protected $pluginsRepository;
    protected $pluginsDir;
    public function __construct($pluginsDir)
    {
        $this->pluginsDir=$pluginsDir;
        $this->pluginsRepository=new Repository($pluginsDir);
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
        if(defined('ABSPATH') && defined('WPINC')){
            $versionFile = ABSPATH . WPINC . '/version.php';
        }else{
            $versionFile = dirname(dirname($this->pluginsDir)) . '/wp-includes/version.php';
        }
        $wp_version = '6.x';
        if ( file_exists( $versionFile ) ) {
            include $versionFile;
        }
        $userAgent = 'WordPress/'.$wp_version  . '; ' . 'http://localhost';
        $formData=http_build_query($data);
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($formData),
            'User-Agent: '.$userAgent
        );
        $apiUrl = ws_config('wordpress.plugins.updater.api_url','http://api.wordpress.org/plugins/update-check/1.1/');
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

        $response = file_get_contents($apiUrl, false, $context);
        if ($response === false) {
            throw new \RuntimeException(
                'Failed to connect to WordPress update API.'
            );
        }

        $update = json_decode($response);

        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_object($update)
            || !isset($update->plugins)
            || !is_object($update->plugins)
        ) {
            throw new \RuntimeException(
                'Invalid response from WordPress update API.'
            );
        }
        foreach ($update->plugins as $slug=>$info){
            if(isset($plugins[$slug])){
                $plugins[$slug]->NewVersion=$info->new_version;
                $plugins[$slug]->UpdatePackage=$info->package;
            }
        }
        return $plugins;
    }


}
