<?php

namespace WpStarter\Wordpress\Middleware;

use WpStarter\Http\Request;
use WpStarter\Routing\Route;

class StartSession extends \WpStarter\Session\Middleware\StartSession
{
    protected $afterSessionSaveAttemptCallbacks=[];
    protected function saveSession($request)
    {
        if (is_wp()){
            add_action('shutdown', function () use ($request) {
                try {
                    parent::saveSession($request);
                } finally {
                    $this->callAfterSessionSaveAttempt();
                }
            });
        }else{
            parent::saveSession($request);
        }
    }

    /**
     * Handle the given request within session state.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \WpStarter\Contracts\Session\Session  $session
     * @param  \Closure  $next
     * @return mixed
     */
    protected function handleRequestWhileBlocking(Request $request, $session, \Closure $next)
    {
        if (! $request->route() instanceof Route) {
            return;
        }

        $lockFor = $request->route() && $request->route()->locksFor()
            ? $request->route()->locksFor()
            : $this->manager->defaultRouteBlockLockSeconds();

        $lock = $this->cache($this->manager->blockDriver())
            ->lock('session:'.$session->getId(), $lockFor)
            ->betweenBlockedAttemptsSleepFor(50);
        $deferRelease=false;
        try {
            $lock->block(
                ! is_null($request->route()->waitsFor())
                    ? $request->route()->waitsFor()
                    : $this->manager->defaultRouteBlockWaitSeconds()
            );

            $response=$this->handleStatefulRequest($request, $session, $next);
            $deferRelease=is_wp();
            return $response;
        } finally {
            if($deferRelease){
                $this->afterSessionSaveAttempt(fn () => $lock?->release());
            }else {
                $lock?->release();
            }
        }
    }

    public function afterSessionSaveAttempt(\Closure $callback){
        $this->afterSessionSaveAttemptCallbacks[]=$callback;
        return $this;
    }
    protected function callAfterSessionSaveAttempt(){
        $callbacks=$this->afterSessionSaveAttemptCallbacks;
        $this->afterSessionSaveAttemptCallbacks=[];
        foreach ($callbacks as $callback){
            $callback();
        }
    }
}
