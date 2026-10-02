<?php

namespace WpStarter\Support\Facades;

/**
 * @method static array preloadedAssets()
 * @method static string|null cspNonce()
 * @method static string useCspNonce(string|null $nonce = null)
 * @method static \WpStarter\Foundation\Vite useIntegrityKey(string|false $key)
 * @method static \WpStarter\Foundation\Vite withEntryPoints(array $entryPoints)
 * @method static \WpStarter\Foundation\Vite mergeEntryPoints(array $entryPoints)
 * @method static \WpStarter\Foundation\Vite useManifestFilename(string $filename)
 * @method static \WpStarter\Foundation\Vite createAssetPathsUsing(callable|null $resolver)
 * @method static string hotFile()
 * @method static \WpStarter\Foundation\Vite useHotFile(string $path)
 * @method static \WpStarter\Foundation\Vite useBuildDirectory(string $path)
 * @method static \WpStarter\Foundation\Vite useScriptTagAttributes(callable|array $attributes)
 * @method static \WpStarter\Foundation\Vite useStyleTagAttributes(callable|array $attributes)
 * @method static \WpStarter\Foundation\Vite usePreloadTagAttributes(callable|array|false $attributes)
 * @method static \WpStarter\Foundation\Vite prefetch(int|null $concurrency = null, string $event = 'load')
 * @method static \WpStarter\Foundation\Vite useWaterfallPrefetching(int|null $concurrency = null)
 * @method static \WpStarter\Foundation\Vite useAggressivePrefetching()
 * @method static \WpStarter\Foundation\Vite usePrefetchStrategy(string|null $strategy, array $config = [])
 * @method static \WpStarter\Support\HtmlString|void reactRefresh()
 * @method static string asset(string $asset, string|null $buildDirectory = null)
 * @method static string content(string $asset, string|null $buildDirectory = null)
 * @method static string|null manifestHash(string|null $buildDirectory = null)
 * @method static bool isRunningHot()
 * @method static string toHtml()
 * @method static void flush()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \WpStarter\Foundation\Vite
 */
class Vite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WpStarter\Foundation\Vite::class;
    }
}
