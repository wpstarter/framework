<?php

namespace WpStarter\Broadcasting;

use WpStarter\Http\Request;
use WpStarter\Routing\Controller;
use WpStarter\Support\Facades\Broadcast;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcastController extends Controller
{
    /**
     * Authenticate the request for channel access.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return \WpStarter\Http\Response
     */
    public function authenticate(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return Broadcast::auth($request);
    }

    /**
     * Authenticate the current user.
     *
     * See: https://pusher.com/docs/channels/server_api/authenticating-users/#user-authentication.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return array|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function authenticateUser(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return Broadcast::resolveAuthenticatedUser($request)
            ?? throw new AccessDeniedHttpException;
    }
}
