<?php

use function PHPStan\Testing\assertType;

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \WpStarter\Database\Eloquent\Casts\AsArrayObject::castUsing([]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Support\Collection<(int|string), mixed>, iterable>',
    \WpStarter\Database\Eloquent\Casts\AsCollection::castUsing([]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \WpStarter\Database\Eloquent\Casts\AsEncryptedArrayObject::castUsing([]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Support\Collection<(int|string), mixed>, iterable>',
    \WpStarter\Database\Eloquent\Casts\AsEncryptedCollection::castUsing([]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Database\Eloquent\Casts\ArrayObject<(int|string), UserType>, iterable<UserType>>',
    \WpStarter\Database\Eloquent\Casts\AsEnumArrayObject::castUsing([\UserType::class]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Support\Collection<(int|string), UserType>, iterable<UserType>>',
    \WpStarter\Database\Eloquent\Casts\AsEnumCollection::castUsing([\UserType::class]),
);

assertType(
    'WpStarter\Contracts\Database\Eloquent\CastsAttributes<WpStarter\Support\Stringable, string|Stringable>',
    \WpStarter\Database\Eloquent\Casts\AsStringable::castUsing([]),
);
