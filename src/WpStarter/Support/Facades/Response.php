<?php

namespace WpStarter\Support\Facades;

use WpStarter\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

/**
 * @method static \WpStarter\Http\Response make(mixed $content = '', int $status = 200, array $headers = [])
 * @method static \WpStarter\Http\Response noContent(int $status = 204, array $headers = [])
 * @method static \WpStarter\Http\Response view(string|array $view, array $data = [], int $status = 200, array $headers = [])
 * @method static \WpStarter\Http\JsonResponse json(mixed $data = [], int $status = 200, array $headers = [], int $options = 0)
 * @method static \WpStarter\Http\JsonResponse jsonp(string $callback, mixed $data = [], int $status = 200, array $headers = [], int $options = 0)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse eventStream(\Closure $callback, array $headers = [], \WpStarter\Http\StreamedEvent|string|null $endStreamWith = '</stream>')
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse stream(callable|null $callback, int $status = 200, array $headers = [])
 * @method static \Symfony\Component\HttpFoundation\StreamedJsonResponse streamJson(array $data, int $status = 200, array $headers = [], int $encodingOptions = 15)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse streamDownload(callable $callback, string|null $name = null, array $headers = [], string|null $disposition = 'attachment')
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download(\SplFileInfo|string $file, string|null $name = null, array $headers = [], string|null $disposition = 'attachment')
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse file(\SplFileInfo|string $file, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse redirectTo(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \WpStarter\Http\RedirectResponse redirectToRoute(\BackedEnum|string $route, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse redirectToAction(array|string $action, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse redirectGuest(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \WpStarter\Http\RedirectResponse redirectToIntended(string $default = '/', int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \WpStarter\Routing\ResponseFactory
 */
class Response extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResponseFactoryContract::class;
    }
}
