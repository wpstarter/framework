<?php

namespace WpStarter\Wordpress\Themes;

use RuntimeException;
use WpStarter\Support\Collection;
use WpStarter\Support\Str;

class Updater
{
    protected $themesRepository;

    protected $themesDir;

    public function __construct($themesDir)
    {
        $this->themesDir = rtrim(str_replace('\\', '/', $themesDir), '/');
        $this->themesRepository = new Repository($this->themesDir);
    }

    /**
     * Check available packages without installing or activating themes.
     *
     * @return Collection<string, Theme>
     */
    public function getUpdates()
    {
        $themes = $this->themesRepository->getThemes();
        $request = [];

        foreach ($themes as $stylesheet => $theme) {
            // These keys match the WordPress theme update-check protocol.
            $request[$stylesheet] = [
                'Name' => $theme->Name,
                'Title' => $theme->Name,
                'Version' => $theme->Version,
                'Author' => $theme->Author,
                'Author URI' => $theme->AuthorURI,
                'UpdateURI' => $theme->UpdateURI,
                'Template' => $theme->Template ?: $stylesheet,
                'Stylesheet' => $stylesheet,
            ];
        }

        $formData = http_build_query([
            'themes' => json_encode(['active' => '', 'themes' => (object) $request]),
            'translations' => json_encode([]),
            'locale' => json_encode([]),
        ]);
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($formData),
            'User-Agent: WordPress/'.$this->wordpressVersion().'; http://localhost',
        ];

        if ($token = ws_config('wordpress.themes.updater.token')) {
            $headers[] = 'Authorization: Bearer '.$token;
        }

        $context = stream_context_create([
            'http' => [
                'header' => implode("\r\n", $headers),
                'method' => 'POST',
                'content' => $formData,
                'timeout' => ws_config('wordpress.themes.updater.timeout', 60),
            ],
        ]);
        $response = $this->request(
            ws_config('wordpress.themes.updater.api_url', 'https://api.wordpress.org/themes/update-check/1.1/'),
            $context
        );

        if ($response === false) {
            throw new RuntimeException('Failed to connect to WordPress theme update API.');
        }

        $update = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($update)
            || ! isset($update['themes']) || ! is_array($update['themes'])) {
            throw new RuntimeException('Invalid response from WordPress theme update API. '.Str::limit($response));
        }

        foreach ($themes as $stylesheet => $theme) {
            unset($theme->NewVersion, $theme->UpdatePackage);

            if (! isset($update['themes'][$stylesheet])) {
                continue;
            }

            $info = $update['themes'][$stylesheet];

            if (! is_array($info) || ! isset($info['new_version'], $info['package'])
                || ! is_string($info['new_version']) || ! is_string($info['package'])) {
                throw new RuntimeException('Invalid update data for theme '.$stylesheet.'.');
            }

            $theme->NewVersion = $info['new_version'];
            $theme->UpdatePackage = $info['package'];
        }

        return $themes;
    }

    protected function request($url, $context)
    {
        return @file_get_contents($url, false, $context);
    }

    protected function wordpressVersion()
    {
        $file = defined('ABSPATH') && defined('WPINC')
            ? ABSPATH.WPINC.'/version.php'
            : dirname($this->themesDir, 2).'/wp-includes/version.php';
        $contents = is_file($file) && is_readable($file) ? file_get_contents($file) : '';

        // Never include WordPress files, even when inspecting a local installation.
        return preg_match('/\$wp_version\s*=\s*([\'"])([^\'"\r\n]+)\1\s*;/', $contents, $match)
            ? $match[2]
            : '6.x';
    }
}
