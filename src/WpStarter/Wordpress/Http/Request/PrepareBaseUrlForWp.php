<?php

namespace WpStarter\Wordpress\Http\Request;

trait PrepareBaseUrlForWp
{
    protected function prepareBaseUrl()
    {
        if(is_wp()) {
            $serverVars = [];
            $serverVars['SCRIPT_FILENAME'] = $this->server->get('SCRIPT_FILENAME', '');
            $serverVars['SCRIPT_NAME'] = $this->server->get('SCRIPT_NAME', '');
            $serverVars['PHP_SELF'] = $this->server->get('PHP_SELF', '');
            $serverVars['ORIG_SCRIPT_NAME'] = $this->server->get('ORIG_SCRIPT_NAME', '');
            $changed = [];
            foreach ($serverVars as $key => $value) {
                if ($filename = basename($value)) {
                    $changed[] = $key;
                    $newValue = str_replace([$filename, '/wp-admin/'], ['index.php', '/'], $value);
                    $this->server->set($key, $newValue);
                }
            }
            $baseUrl = parent::prepareBaseUrl();
            foreach ($changed as $key) {
                $this->server->set($key, $serverVars[$key]);
            }
        }else{
            $baseUrl = parent::prepareBaseUrl();
        }
        return  $baseUrl;
    }
}
