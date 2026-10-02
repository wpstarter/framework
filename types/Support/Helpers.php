<?php

use function PHPStan\Testing\assertType;

/** @var bool|float|int|string|null $value */
if (ws_filled($value)) {
    assertType('bool|float|int|non-empty-string', $value);
} else {
    assertType('string|null', $value);
}

if (ws_blank($value)) {
    assertType('string|null', $value);
} else {
    assertType('bool|float|int|non-empty-string', $value);
}

assertType('User', ws_object_get(new User(), null));
assertType('User', ws_object_get(new User(), ''));
assertType('mixed', ws_object_get(new User(), 'name'));

assertType('1', ws_once(fn () => 1));
assertType('null', ws_once(function () { /** @phpstan-ignore function.void (testing void) */
}));

assertType('WpStarter\Support\Optional', ws_optional());
assertType('null', ws_optional(null, fn () => 1));
assertType('1', ws_optional('foo', function ($value) {
    assertType("'foo'", $value);

    return 1;
}));

assertType('1', ws_retry(5, fn () => 1));

assertType('object', ws_str());
assertType('WpStarter\Support\Stringable', ws_str('foo'));

assertType('User', ws_tap(new User(), function ($user) {
    assertType('User', $user);
}));
assertType('WpStarter\Support\HigherOrderTapProxy', ws_tap(new User()));

function testThrowIf(float|int $foo, ?DateTime $bar = null): void
{
    ws_rescue(fn () => assertType('never', ws_throw_if(true, Exception::class)));
    assertType('false', ws_throw_if(false, Exception::class));
    assertType('false', ws_throw_if(empty($foo)));
    ws_throw_if(is_float($foo));
    assertType('int', $foo);
    ws_throw_if($foo == false);
    assertType('int<min, -1>|int<1, max>', $foo);

    // Truthy/falsey argument
    ws_throw_if($bar);
    assertType('null', $bar);
    assertType('null', ws_throw_if(null, Exception::class));
    assertType("''", ws_throw_if('', Exception::class));
    ws_rescue(fn () => assertType('never', ws_throw_if('foo', Exception::class)));
}

function testThrowUnless(float|int $foo, ?DateTime $bar = null): void
{
    assertType('true', ws_throw_unless(true, Exception::class));
    ws_rescue(fn () => assertType('never', ws_throw_unless(false, Exception::class)));
    assertType('true', ws_throw_unless(empty($foo)));
    ws_throw_unless(is_int($foo));
    assertType('int', $foo);
    ws_throw_unless($foo == false);
    assertType('0', $foo);
    ws_throw_unless($bar instanceof DateTime);
    assertType('DateTime', $bar);

    // Truthy/falsey argument
    ws_rescue(fn () => assertType('never', ws_throw_unless(null, Exception::class)));
    ws_rescue(fn () => assertType('never', ws_throw_unless('', Exception::class)));
    assertType("'foo'", ws_throw_unless('foo', Exception::class));
}

assertType('1', ws_transform('filled', fn () => 1, true));
assertType('1', ws_transform(['filled'], fn () => 1));
assertType('null', ws_transform('', fn () => 1));
assertType('true', ws_transform('', fn () => 1, true));
assertType('true', ws_transform('', fn () => 1, fn () => true));

assertType('User', ws_with(new User()));
assertType('bool', ws_with(new User())->save());
assertType('10', ws_with(new User(), function ($user) {
    assertType('User', $user);

    return 10;
}));

assertType('SupportLazyClass', ws_lazy(SupportLazyClass::class, function (SupportLazyClass $instance) {
    return [];
}));
assertType('SupportLazyClass', ws_proxy(SupportLazyClass::class, function (SupportLazyClass $proxy) {
    return new SupportLazyClass();
}));
assertType('SupportLazyClass', ws_lazy(fn (SupportLazyClass $instance) => []));
assertType('SupportLazyClass', ws_proxy(fn (SupportLazyClass $proxy) => new SupportLazyClass));
assertType('SupportLazyClass', ws_proxy(fn (): SupportLazyClass => new SupportLazyClass));

class SupportLazyClass
{
    //
}
