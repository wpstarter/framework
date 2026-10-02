<?php

namespace WpStarter\Foundation;

use WpStarter\Support\HtmlString;
use WpStarter\Support\Str;

class Mix
{
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \WpStarter\Support\HtmlString|string
     *
     * @throws \WpStarter\Foundation\MixManifestNotFoundException|\WpStarter\Foundation\MixFileNotFoundException
     */
    public function __invoke($path, $manifestDirectory = '')
    {
        static $manifests = [];

        if (! str_starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! str_starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (is_file(ws_public_path($manifestDirectory.'/hot'))) {
            $url = rtrim(file_get_contents(ws_public_path($manifestDirectory.'/hot')));

            $customUrl = ws_app('config')->get('app.mix_hot_proxy_url');

            if (! empty($customUrl)) {
                return new HtmlString("{$customUrl}{$path}");
            }

            if (Str::startsWith($url, ['http://', 'https://'])) {
                return new HtmlString(Str::after($url, ':').$path);
            }

            return new HtmlString("//localhost:8080{$path}");
        }

        $manifestPath = ws_public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! is_file($manifestPath)) {
                throw new MixManifestNotFoundException("Mix manifest not found at: {$manifestPath}");
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            $exception = new MixFileNotFoundException("Unable to locate Mix file: {$path}.");

            if (! ws_app('config')->get('app.debug')) {
                ws_report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return new HtmlString(ws_app('config')->get('app.mix_url').$manifestDirectory.$manifest[$path]);
    }
}
