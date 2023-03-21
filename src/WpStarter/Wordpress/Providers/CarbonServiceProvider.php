<?php

namespace WpStarter\Wordpress\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use WpStarter\Contracts\Events\Dispatcher as DispatcherContract;
use WpStarter\Events\Dispatcher;
use WpStarter\Events\EventDispatcher;
use WpStarter\Support\Carbon as WpStarterCarbon;
use WpStarter\Support\Facades\Date;
use Throwable;
class CarbonServiceProvider extends \WpStarter\Support\ServiceProvider
{
    /** @var callable|null */
    protected $appGetter = null;

    /** @var callable|null */
    protected $localeGetter = null;

    public function setAppGetter(?callable $appGetter): void
    {
        $this->appGetter = $appGetter;
    }

    public function setLocaleGetter(?callable $localeGetter): void
    {
        $this->localeGetter = $localeGetter;
    }

    public function boot()
    {
        $this->updateLocale();

        if (!$this->app->bound('events')) {
            return;
        }

        $service = $this;
        $events = $this->app['events'];

        if ($this->isEventDispatcher($events)) {
            $events->listen(class_exists('WpStarter\Foundation\Events\LocaleUpdated') ? 'WpStarter\Foundation\Events\LocaleUpdated' : 'locale.changed', function () use ($service) {
                $service->updateLocale();
            });
        }
    }

    public function updateLocale()
    {
        $locale = $this->getLocale();

        if ($locale === null) {
            return;
        }

        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);

        if (class_exists(WpStarterCarbon::class)) {
            WpStarterCarbon::setLocale($locale);
        }

        if (class_exists(Date::class)) {
            try {
                $root = Date::getFacadeRoot();
                $root->setLocale($locale);
            } catch (Throwable $e) {
                // Non Carbon class in use in Date facade
            }
        }
    }

    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }

    protected function getLocale()
    {
        if ($this->localeGetter) {
            return ($this->localeGetter)();
        }

        $app = $this->getApp();
        $app = $app && method_exists($app, 'getLocale')
            ? $app
            : $this->getGlobalApp('translator');

        return $app ? $app->getLocale() : null;
    }

    protected function getApp()
    {
        if ($this->appGetter) {
            return ($this->appGetter)();
        }

        return $this->app ?? $this->getGlobalApp();
    }

    protected function getGlobalApp(...$args)
    {
        return \function_exists('app') ? \app(...$args) : null;
    }

    protected function isEventDispatcher($instance)
    {
        return $instance instanceof EventDispatcher
            || $instance instanceof Dispatcher
            || $instance instanceof DispatcherContract;
    }
}
