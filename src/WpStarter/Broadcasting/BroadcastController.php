<?php

namespace WpStarter\Broadcasting;

use WpStarter\Http\Request;
use WpStarter\Routing\Controller;
use WpStarter\Support\Facades\Broadcast;

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
}
