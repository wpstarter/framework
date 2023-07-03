<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Support\Arr;

class RouteGroup
{
    /**
     * Merge route groups into a new array.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    public static function merge($new, $old)
    {
        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        if (isset($new['controller'])) {
            unset($old['controller']);
        }

        $new = array_merge($new, [
            'name' => static::formatName($new, $old),
            'namespace' => static::formatNamespace($new, $old),
        ]);

        return array_merge_recursive(Arr::except(
            $old, ['name','namespace']
        ), $new);
    }

    /**
     * Format the namespace for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatNamespace($new, $old)
    {
        if (isset($new['namespace'])) {
            return isset($old['namespace']) && strpos($new['namespace'], '\\') !== 0
                ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                : trim($new['namespace'], '\\');
        }

        return $old['namespace'] ?? null;
    }
    /**
     * Format the "name" for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string
     */
    protected static function formatName($new, $old)
    {
        if (isset($old['name'])) {
            $new['name'] = $old['name'].($new['name'] ?? '');
        }

        return $new['name'] ?? '';
    }
}
