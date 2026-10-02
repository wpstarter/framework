<?php

use WpStarter\Support\Timebox;

use function PHPStan\Testing\assertType;

assertType('1', (new Timebox)->call(function ($timebox) {
    assertType('WpStarter\Support\Timebox', $timebox);

    return 1;
}, 1));
