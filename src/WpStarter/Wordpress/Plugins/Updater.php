<?php

namespace WpStarter\Wordpress\Plugins;

use WpStarter\Support\Collection;


class Updater
{
    protected $pluginsRepository;
    public function __construct($pluginsDir)
    {
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
        $userAgent = 'WordPress/6.2.2'  . '; ' . 'http://localhost';
        $formData=http_build_query($data);
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($formData),
            'User-Agent: '.$userAgent
        );

        $options = array(
            'http' => array(
                'header'  => implode("\r\n", $headers),
                'method'  => 'POST',
                'content' => $formData,
            ),
        );

        $context = stream_context_create($options);

        $response = file_get_contents('http://api.wordpress.org/plugins/update-check/1.1/', false, $context);
        $update=json_decode($response);
        foreach ($update->plugins as $slug=>$info){
            if(isset($plugins[$slug])){
                $plugins[$slug]->NewVersion=$info->new_version;
                $plugins[$slug]->UpdatePackage=$info->package;
            }
        }
        return $plugins;
    }


}
