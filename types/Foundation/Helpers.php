<?php

use WpStarter\Config\Repository;

use function PHPStan\Testing\assertType;

assertType('WpStarter\Foundation\Application', app());
assertType('mixed', app('foo'));
assertType('WpStarter\Config\Repository', app(Repository::class));

assertType('WpStarter\Contracts\Auth\Factory', ws_auth());
assertType('WpStarter\Contracts\Auth\Guard', ws_auth('foo'));

assertType('WpStarter\Cache\CacheManager', ws_cache());
assertType('bool', ws_cache(['foo' => 'bar'], 42));
assertType('mixed', ws_cache('foo', 42));

assertType('WpStarter\Config\Repository', ws_config());
assertType('null', ws_config(['foo' => 'bar']));
assertType('mixed', ws_config('foo'));

assertType('WpStarter\Log\Context\Repository', ws_context());
assertType('WpStarter\Log\Context\Repository', ws_context(['foo' => 'bar']));
assertType('mixed', ws_context('foo'));

assertType('WpStarter\Cookie\CookieJar', ws_cookie());
assertType('Symfony\Component\HttpFoundation\Cookie', ws_cookie('foo'));

assertType('WpStarter\Foundation\Bus\PendingDispatch', ws_dispatch('foo'));
assertType('WpStarter\Foundation\Bus\PendingClosureDispatch', ws_dispatch(fn () => 1));

assertType('Psr\Log\LoggerInterface', ws_logger());
assertType('null', ws_logger('foo'));

assertType('WpStarter\Log\LogManager', ws_logs());
assertType('Psr\Log\LoggerInterface', ws_logs('foo'));

assertType('123|null', ws_rescue(fn () => 123));
assertType('123|345', ws_rescue(fn () => 123, 345));
assertType('123|345', ws_rescue(fn () => 123, fn () => 345));

assertType('WpStarter\Routing\Redirector', ws_redirect());
assertType('WpStarter\Http\RedirectResponse', ws_redirect('foo'));

assertType('mixed', ws_resolve('foo'));
assertType('WpStarter\Config\Repository', ws_resolve(Repository::class));

assertType('WpStarter\Http\Request', ws_request());
assertType('mixed', ws_request('foo'));
assertType('array<string, mixed>', ws_request(['foo', 'bar']));

assertType('WpStarter\Contracts\Routing\ResponseFactory', ws_response());
assertType('WpStarter\Http\Response', ws_response('foo'));

assertType('WpStarter\Session\SessionManager', ws_session());
assertType('mixed', ws_session('foo'));
assertType('null', ws_session(['foo' => 'bar']));

assertType('WpStarter\Contracts\Translation\Translator', ws_trans());
assertType('array|string', ws_trans('foo'));

assertType('WpStarter\Contracts\Validation\Factory', ws_validator());
assertType('WpStarter\Contracts\Validation\Validator', ws_validator([]));

assertType('WpStarter\Contracts\View\Factory', ws_view());
assertType('WpStarter\Contracts\View\View', ws_view('foo'));

assertType('WpStarter\Contracts\Routing\UrlGenerator', ws_url());
assertType('string', ws_url('foo'));
