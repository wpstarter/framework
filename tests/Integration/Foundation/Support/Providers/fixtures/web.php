<?php

use WpStarter\Support\Facades\Route;

Route::get('/{user}', fn () => response('', 404));
