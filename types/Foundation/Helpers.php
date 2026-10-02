<?php

use WpStarter\Config\Repository;

use function PHPStan\Testing\assertType;

assertType('WpStarter\Foundation\Application', app());
assertType('mixed', app('foo'));
assertType('WpStarter\Config\Repository', app(Repository::class));

assertType('WpStarter\Contracts\Auth\Factory', auth());
assertType('WpStarter\Contracts\Auth\Guard', auth('foo'));

assertType('WpStarter\Cache\CacheManager', cache());
assertType('bool', cache(['foo' => 'bar'], 42));
assertType('mixed', cache('foo', 42));

assertType('WpStarter\Config\Repository', config());
assertType('null', config(['foo' => 'bar']));
assertType('mixed', config('foo'));

assertType('WpStarter\Log\Context\Repository', context());
assertType('WpStarter\Log\Context\Repository', context(['foo' => 'bar']));
assertType('mixed', context('foo'));

assertType('WpStarter\Cookie\CookieJar', cookie());
assertType('Symfony\Component\HttpFoundation\Cookie', cookie('foo'));

assertType('WpStarter\Foundation\Bus\PendingDispatch', dispatch('foo'));
assertType('WpStarter\Foundation\Bus\PendingClosureDispatch', dispatch(fn () => 1));

assertType('Psr\Log\LoggerInterface', logger());
assertType('null', logger('foo'));

assertType('WpStarter\Log\LogManager', logs());
assertType('Psr\Log\LoggerInterface', logs('foo'));

assertType('123|null', rescue(fn () => 123));
assertType('123|345', rescue(fn () => 123, 345));
assertType('123|345', rescue(fn () => 123, fn () => 345));

assertType('WpStarter\Routing\Redirector', redirect());
assertType('WpStarter\Http\RedirectResponse', redirect('foo'));

assertType('mixed', resolve('foo'));
assertType('WpStarter\Config\Repository', resolve(Repository::class));

assertType('WpStarter\Http\Request', request());
assertType('mixed', request('foo'));
assertType('array<string, mixed>', request(['foo', 'bar']));

assertType('WpStarter\Contracts\Routing\ResponseFactory', response());
assertType('WpStarter\Http\Response', response('foo'));

assertType('WpStarter\Session\SessionManager', session());
assertType('mixed', session('foo'));
assertType('null', session(['foo' => 'bar']));

assertType('WpStarter\Contracts\Translation\Translator', trans());
assertType('array|string', trans('foo'));

assertType('WpStarter\Contracts\Validation\Factory', validator());
assertType('WpStarter\Contracts\Validation\Validator', validator([]));

assertType('WpStarter\Contracts\View\Factory', view());
assertType('WpStarter\Contracts\View\View', view('foo'));

assertType('WpStarter\Contracts\Routing\UrlGenerator', url());
assertType('string', url('foo'));
