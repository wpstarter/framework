<?php

use WpStarter\Support\Stringable;

use function PHPStan\Testing\assertType;

$stringable = new Stringable();

assertType('WpStarter\Support\Collection<int, string>', $stringable->explode(''));

assertType('WpStarter\Support\Collection<int, string>', $stringable->split(1));

assertType('WpStarter\Support\Collection<int, string>', $stringable->ucsplit());
