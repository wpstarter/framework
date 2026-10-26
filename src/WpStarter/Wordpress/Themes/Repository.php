<?php

namespace WpStarter\Wordpress\Themes;

use WpStarter\Support\Collection;

class Repository
{
    const KB_IN_BYTES = 1024;

    protected $themesDir;

    protected $themes;

    public function __construct($themesDir)
    {
        $this->themesDir = rtrim(str_replace('\\', '/', $themesDir), '/');
    }

    /**
     * @return Collection<string, Theme>
     */
    public function getThemes()
    {
        if ($this->themes !== null) {
            return $this->themes;
        }

        $themes = [];

        foreach ($this->directories($this->themesDir) as $directory) {
            $path = $this->themesDir.'/'.$directory;

            if (is_file($path.'/style.css')) {
                $stylesheets = [$directory];
            } else {
                $stylesheets = array_map(function ($child) use ($directory) {
                    return $directory.'/'.$child;
                }, $this->directories($path));
            }

            foreach ($stylesheets as $stylesheet) {
                $file = $this->themesDir.'/'.$stylesheet.'/style.css';

                if (! is_file($file) || ! is_readable($file)) {
                    continue;
                }

                $theme = $this->getThemeData($file);

                if ($theme->Name !== '') {
                    $themes[$stylesheet] = $theme;
                }
            }
        }

        uasort($themes, function ($a, $b) {
            return strnatcasecmp($a->Name, $b->Name);
        });

        return $this->themes = new Collection($themes);
    }

    /**
     * Read metadata without executing, translating, or rendering theme code.
     *
     * @return Theme
     */
    public function getThemeData($themeFile)
    {
        $data = $this->getFileData($themeFile, [
            'Name' => 'Theme Name',
            'ThemeURI' => 'Theme URI',
            'Description' => 'Description',
            'Author' => 'Author',
            'AuthorURI' => 'Author URI',
            'Version' => 'Version',
            'Template' => 'Template',
            'Status' => 'Status',
            'Tags' => 'Tags',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path',
            'RequiresWP' => 'Requires at least',
            'RequiresPHP' => 'Requires PHP',
            'UpdateURI' => 'Update URI',
        ]);

        $data['Status'] = $data['Status'] ?: 'publish';
        $data['Tags'] = array_values(array_filter(array_map('trim', explode(',', strip_tags($data['Tags'])))));
        $data['Stylesheet'] = $this->themeBasename(dirname(str_replace('\\', '/', $themeFile)));

        return new Theme($data);
    }

    public function getFileData($file, $defaultHeaders)
    {
        // WordPress reads only the first 8 KB and accepts CR-only line endings.
        $contents = @file_get_contents($file, false, null, 0, 8 * static::KB_IN_BYTES);
        $contents = str_replace("\r", "\n", $contents === false ? '' : $contents);

        foreach ($defaultHeaders as $field => $header) {
            if (preg_match('/^(?:[ \t]*<\?php)?[ \t\/*#@]*'.preg_quote($header, '/').':(.*)$/mi', $contents, $match)) {
                $defaultHeaders[$field] = trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $match[1]));
            } else {
                $defaultHeaders[$field] = '';
            }
        }

        return $defaultHeaders;
    }

    public function themeBasename($file)
    {
        $file = str_replace('\\', '/', $file);
        $file = preg_replace('|(?<=.)/+|', '/', $file);
        $root = preg_replace('|(?<=.)/+|', '/', $this->themesDir);

        if (isset($file[1]) && $file[1] === ':') {
            $file = ucfirst($file);
            $root = ucfirst($root);
        }

        return trim(preg_replace('#^'.preg_quote($root, '#').'/#', '', $file), '/');
    }

    protected function directories($path)
    {
        return array_values(array_filter(@scandir($path) ?: [], function ($name) use ($path) {
            return $name[0] !== '.' && $name !== 'CVS' && is_dir($path.'/'.$name);
        }));
    }
}
