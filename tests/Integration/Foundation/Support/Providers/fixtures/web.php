<?php

use WpStarter\Support\Facades\Route;

Route::get('/{user}', fn () => ws_response('', 404));
