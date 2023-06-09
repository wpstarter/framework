<?php

namespace WpStarter\Wordpress\Plugins;

use WpStarter\Support\Collection;

class Repository
{
    const KB_IN_BYTES=1024;
    protected $pluginsDir;
    protected $plugins;
    public function __construct($pluginsDir)
    {
        $this->pluginsDir=$pluginsDir;
    }
    /**
     * @return Collection|Plugin[]
     */
    function getPlugins( ) {
        if($this->plugins){
            return $this->plugins;
        }
        /**
         * @var Plugin[] $wpPlugins
         */
        $wpPlugins  = array();
        $pluginRoot = $this->pluginsDir;

        // Files in wp-content/plugins directory.
        $pluginsDir  = @opendir( $pluginRoot );
        $pluginFiles = array();

        if ( $pluginsDir ) {
            while ( ( $file = readdir( $pluginsDir ) ) !== false ) {
                if ( '.' === substr( $file, 0, 1 ) ) {
                    continue;
                }

                if ( is_dir( $pluginRoot . '/' . $file ) ) {
                    $pluginsSubDir = @opendir( $pluginRoot . '/' . $file );

                    if ( $pluginsSubDir ) {
                        while ( ( $subfile = readdir( $pluginsSubDir ) ) !== false ) {
                            if ( '.' === substr( $subfile, 0, 1 ) ) {
                                continue;
                            }

                            if ( '.php' === substr( $subfile, -4 ) ) {
                                $pluginFiles[] = "$file/$subfile";
                            }
                        }

                        closedir( $pluginsSubDir );
                    }
                } else {
                    if ( '.php' === substr( $file, -4 ) ) {
                        $pluginFiles[] = $file;
                    }
                }
            }

            closedir( $pluginsDir );
        }

        if ( empty( $pluginFiles ) ) {
            return $wpPlugins;
        }

        foreach ( $pluginFiles as $plugin_file ) {
            if ( ! is_readable( "$pluginRoot/$plugin_file" ) ) {
                continue;
            }

            // Do not apply markup/translate as it will be cached.
            $pluginData = $this->getPluginData( "$pluginRoot/$plugin_file" );

            if ( empty( $pluginData->Name ) ) {
                continue;
            }

            $wpPlugins[ $this->pluginBasename( $plugin_file ) ] = $pluginData;
        }
        uasort( $wpPlugins, function( $a, $b ) {
            return strnatcasecmp( $a->Name, $b->Name );
        } );

        $this->plugins=new Collection($wpPlugins);
        return $this->plugins;
    }

    function getFileData($file, $default_headers ) {
        // Pull only the first 8 KB of the file in.
        $file_data = file_get_contents( $file, false, null, 0, 8 * static::KB_IN_BYTES );

        if ( false === $file_data ) {
            $file_data = '';
        }

        // Make sure we catch CR-only line endings.
        $file_data = str_replace( "\r", "\n", $file_data );

        $extra_headers = [];
        if ( $extra_headers ) {
            $extra_headers = array_combine( $extra_headers, $extra_headers ); // Keys equal values.
            $all_headers   = array_merge( $extra_headers, (array) $default_headers );
        } else {
            $all_headers = $default_headers;
        }

        foreach ( $all_headers as $field => $regex ) {
            if ( preg_match( '/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
                $all_headers[ $field ] = $this->cleanupHeaderComment( $match[1] );
            } else {
                $all_headers[ $field ] = '';
            }
        }

        return $all_headers;
    }

    function getPluginData($plugin_file ) {

        $default_headers = array(
            'Name'        => 'Plugin Name',
            'PluginURI'   => 'Plugin URI',
            'Version'     => 'Version',
            'Description' => 'Description',
            'Author'      => 'Author',
            'AuthorURI'   => 'Author URI',
            'TextDomain'  => 'Text Domain',
            'DomainPath'  => 'Domain Path',
            'Network'     => 'Network',
            'RequiresWP'  => 'Requires at least',
            'RequiresPHP' => 'Requires PHP',
            'UpdateURI'   => 'Update URI',
            // Site Wide Only is deprecated in favor of Network.
            '_sitewide'   => 'Site Wide Only',
        );

        $plugin_data = $this->getFileData( $plugin_file, $default_headers );

        // Site Wide Only is the old header for Network.
        if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
            $plugin_data['Network'] = $plugin_data['_sitewide'];
        }
        $plugin_data['Network'] = ( 'true' === strtolower( $plugin_data['Network'] ) );
        unset( $plugin_data['_sitewide'] );

        // If no text domain is defined fall back to the plugin slug.
        if ( ! $plugin_data['TextDomain'] ) {
            $plugin_slug = dirname( $this->pluginBasename( $plugin_file ) );
            if ( '.' !== $plugin_slug && false === strpos( $plugin_slug, '/' ) ) {
                $plugin_data['TextDomain'] = $plugin_slug;
            }
        }
        $plugin_data['Title']      = $plugin_data['Name'];
        $plugin_data['AuthorName'] = $plugin_data['Author'];

        return new Plugin($plugin_data);
    }
    function pluginBasename($file ) {

        // $wp_plugin_paths contains normalized paths.
        $file = $this->normalizePath( $file );


        $pluginDir    = $this->normalizePath($this->pluginsDir);
        $muPluginDir = $this->normalizePath(dirname($this->pluginsDir).'/mu-plugins');

        // Get relative path from plugins directory.
        $file = preg_replace( '#^' . preg_quote( $pluginDir, '#' ) . '/|^' . preg_quote( $muPluginDir, '#' ) . '/#', '', $file );
        return trim( $file, '/' );
    }

    protected function cleanupHeaderComment($str ) {
        return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $str ) );
    }
    protected function normalizePath($path ) {
        $wrapper = '';

        // Standardize all paths to use '/'.
        $path = str_replace( '\\', '/', $path );

        // Replace multiple slashes down to a singular, allowing for network shares having two slashes.
        $path = preg_replace( '|(?<=.)/+|', '/', $path );

        // Windows paths should uppercase the drive letter.
        if ( ':' === substr( $path, 1, 1 ) ) {
            $path = ucfirst( $path );
        }

        return $wrapper . $path;
    }
}
