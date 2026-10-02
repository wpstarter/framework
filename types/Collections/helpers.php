<?php

use function PHPStan\Testing\assertType;

assertType("'foo'", ws_value('foo', 42));
assertType('42', ws_value(fn () => 42));
assertType('42', ws_value(function ($foo) {
    assertType('true', $foo);

    return 42;
}, true));

assertType("'foo'", ws_when(true, 'foo'));
assertType("'foo'", ws_when(true, 'foo', 42));
assertType('null', ws_when(false, 'foo'));
assertType('42', ws_when(false, 'foo', 42));
assertType("'foo'", ws_when(true, fn () => 'foo'));
assertType("'foo'", ws_when(true, fn () => 'foo', fn () => 42));
assertType('null', ws_when(false, fn () => 'foo'));
assertType('42', ws_when(false, fn () => 'foo', fn () => 42));
assertType("'foo'", ws_when(1, 'foo', 42));
assertType("'foo'", ws_when(42, 'foo'));
assertType('null', ws_when(0, 'foo'));
assertType('null', ws_when(-42, 'foo'));
assertType('null', ws_when(null, 'foo'));
assertType('42', ws_when(['foo'], 42));
assertType('null', ws_when([], 42));
assertType('42|null', ws_when(random_int(0, 1), 42));
assertType('42|1337', ws_when(random_int(0, 1), 42, 1337));
assertType("array{'bar'}|array{'foo'}", ws_when(random_int(0, 1), ['foo'], ['bar']));
assertType('42|null', ws_when(fn () => random_int(0, 1), 42));
assertType('42|1337', ws_when(fn () => random_int(0, 1), 42, 1337));
