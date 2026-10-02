<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "reverb", "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => ws_env('BROADCAST_CONNECTION', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over WebSockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => ws_env('REVERB_APP_KEY'),
            'secret' => ws_env('REVERB_APP_SECRET'),
            'app_id' => ws_env('REVERB_APP_ID'),
            'options' => [
                'host' => ws_env('REVERB_HOST'),
                'port' => ws_env('REVERB_PORT', 443),
                'scheme' => ws_env('REVERB_SCHEME', 'https'),
                'useTLS' => ws_env('REVERB_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => ws_env('PUSHER_APP_KEY'),
            'secret' => ws_env('PUSHER_APP_SECRET'),
            'app_id' => ws_env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => ws_env('PUSHER_APP_CLUSTER'),
                'host' => ws_env('PUSHER_HOST') ?: 'api-'.ws_env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => ws_env('PUSHER_PORT', 443),
                'scheme' => ws_env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => ws_env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => ws_env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
