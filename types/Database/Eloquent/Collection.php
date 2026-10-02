<?php

use function PHPStan\Testing\assertType;

$collection = User::all();
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection);

assertType('User|null', $collection->find(1));
assertType("'string'|User", $collection->find(1, 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->find([1]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->load('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->load(['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->load(['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->load(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAggregate('string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string'], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string' => ['foo' => fn ($q) => $q]], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string'], 'string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}], 'string', 'string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadCount('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadCount(['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadCount(['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadCount(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMax('string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMax(['string'], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMax(['string' => ['foo' => fn ($q) => $q]], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMax(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}], 'string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMin('string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMin(['string'], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMin(['string' => ['foo' => fn ($q) => $q]], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMin(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}], 'string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadSum('string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadSum(['string'], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadSum(['string' => ['foo' => fn ($q) => $q]], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadSum(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}], 'string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAvg('string', 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAvg(['string'], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAvg(['string' => ['foo' => fn ($q) => $q]], 'string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadAvg(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}], 'string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadExists('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadExists(['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadExists(['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadExists(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMissing('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMissing(['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMissing(['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMissing(['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorph('string', ['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorph('string', ['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorph('string', ['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorphCount('string', ['string']));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorphCount('string', ['string' => ['foo' => fn ($q) => $q]]));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->loadMorphCount('string', ['string' => function ($query) {
    // assertType('WpStarter\Database\Eloquent\Relations\Relation<*,*,*>', $query);
}]));

assertType('bool', $collection->contains(function ($user) {
    assertType('User', $user);

    return true;
}));
assertType('bool', $collection->contains('string', '=', 'string'));

assertType('array<int, (int|string)>', $collection->modelKeys());

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->merge($collection));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->merge([new User]));

assertType(
    'WpStarter\Support\Collection<int, User>',
    $collection->map(function ($user, $int) {
        assertType('User', $user);
        assertType('int', $int);

        return new User;
    })
);

assertType(
    'WpStarter\Support\Collection<int, User>',
    $collection->mapWithKeys(function ($user, $int) {
        assertType('User', $user);
        assertType('int', $int);

        return [new User];
    })
);
assertType(
    'WpStarter\Support\Collection<string, User>',
    $collection->mapWithKeys(function ($user, $int) {
        return ['string' => new User];
    })
);

assertType(
    'WpStarter\Database\Eloquent\Collection<int, User>',
    $collection->fresh()
);
assertType(
    'WpStarter\Database\Eloquent\Collection<int, User>',
    $collection->fresh('string')
);
assertType(
    'WpStarter\Database\Eloquent\Collection<int, User>',
    $collection->fresh(['string'])
);

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->diff($collection));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->diff([new User]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->intersect($collection));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->intersect([new User]));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->unique());
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->unique(function ($user, $int) {
    assertType('User', $user);
    assertType('int', $int);

    return $user->getTable();
}));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->unique('string'));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->only(null));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->only(['string']));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->except(null));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->except(['string']));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->makeHidden('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->makeHidden(['string']));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->makeVisible('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->makeVisible(['string']));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->append('string'));
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->append(['string']));

assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->unique());
assertType('WpStarter\Database\Eloquent\Collection<int, User>', $collection->uniqueStrict());

assertType('array<User>', $collection->getDictionary());
assertType('array<User>', $collection->getDictionary($collection));
assertType('array<User>', $collection->getDictionary([new User]));

assertType('WpStarter\Support\Collection<(int|string), mixed>', $collection->pluck('string'));
assertType('WpStarter\Support\Collection<(int|string), mixed>', $collection->pluck(['string']));

assertType('WpStarter\Support\Collection<int, int>', $collection->keys());

assertType('WpStarter\Support\Collection<int, WpStarter\Support\Collection<int, int|User>>', $collection->zip([1]));
assertType('WpStarter\Support\Collection<int, WpStarter\Support\Collection<int, string|User>>', $collection->zip(['string']));

assertType('WpStarter\Support\Collection<int, mixed>', $collection->collapse());

assertType('WpStarter\Support\Collection<int, mixed>', $collection->flatten());
assertType('WpStarter\Support\Collection<int, mixed>', $collection->flatten(4));

assertType('WpStarter\Support\Collection<User, int>', $collection->flip());

assertType('WpStarter\Support\Collection<int, int|User>', $collection->pad(2, 0));
assertType('WpStarter\Support\Collection<int, string|User>', $collection->pad(2, 'string'));

assertType('array<int, mixed>', $collection->getQueueableIds());

assertType('array<int, string>', $collection->getQueueableRelations());

assertType('WpStarter\Database\Eloquent\Builder<User>', $collection->toQuery());
