<?php

namespace WpStarter\Wordpress\Configuration;
class Middleware extends \WpStarter\Foundation\Configuration\Middleware
{
    public function getMiddlewareGroups()
    {
        $middleware = [
            'web' => array_values(array_filter([
                \WpStarter\Cookie\Middleware\EncryptCookies::class,
                \WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \WpStarter\Wordpress\Middleware\StartSession::class,
                \WpStarter\View\Middleware\ShareErrorsFromSession::class,
                \WpStarter\Foundation\Http\Middleware\ValidateCsrfToken::class,
                \WpStarter\Routing\Middleware\SubstituteBindings::class,
                $this->authenticatedSessions ? 'auth.session' : null,
            ])),

            'api' => array_values(array_filter([
                $this->statefulApi ? \WpStarter\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class : null,
                $this->apiLimiter ? 'throttle:'.$this->apiLimiter : null,
                \WpStarter\Routing\Middleware\SubstituteBindings::class,
            ])),
        ];

        $middleware = array_merge($middleware, $this->groups);

        foreach ($middleware as $group => $groupedMiddleware) {
            foreach ($groupedMiddleware as $index => $groupMiddleware) {
                if (isset($this->groupReplacements[$group][$groupMiddleware])) {
                    $middleware[$group][$index] = $this->groupReplacements[$group][$groupMiddleware];
                }
            }
        }

        foreach ($this->groupRemovals as $group => $removals) {
            $middleware[$group] = array_values(array_filter(
                array_diff($middleware[$group] ?? [], $removals)
            ));
        }

        foreach ($this->groupPrepends as $group => $prepends) {
            $middleware[$group] = array_values(array_filter(
                array_unique(array_merge($prepends, $middleware[$group] ?? []))
            ));
        }

        foreach ($this->groupAppends as $group => $appends) {
            $middleware[$group] = array_values(array_filter(
                array_unique(array_merge($middleware[$group] ?? [], $appends))
            ));
        }

        return $middleware;
    }
}
