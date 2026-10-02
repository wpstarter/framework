<?php

namespace WpStarter\Types\Relations;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\Relations\BelongsTo;
use WpStarter\Database\Eloquent\Relations\BelongsToMany;
use WpStarter\Database\Eloquent\Relations\HasMany;
use WpStarter\Database\Eloquent\Relations\HasManyThrough;
use WpStarter\Database\Eloquent\Relations\HasOne;
use WpStarter\Database\Eloquent\Relations\HasOneThrough;
use WpStarter\Database\Eloquent\Relations\MorphMany;
use WpStarter\Database\Eloquent\Relations\MorphOne;
use WpStarter\Database\Eloquent\Relations\MorphTo;
use WpStarter\Database\Eloquent\Relations\MorphToMany;
use WpStarter\Database\Eloquent\Relations\Relation;

use function PHPStan\Testing\assertType;

function test(User $user, Post $post, Comment $comment, ChildUser $child): void
{
    assertType('WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Address, WpStarter\Types\Relations\User>', $user->address());
    assertType('WpStarter\Types\Relations\Address|null', $user->address()->getResults());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Address>', $user->address()->get());
    assertType('WpStarter\Types\Relations\Address', $user->address()->make());
    assertType('WpStarter\Types\Relations\Address', $user->address()->create());
    assertType('WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Address, WpStarter\Types\Relations\ChildUser>', $child->address());
    assertType('WpStarter\Types\Relations\Address', $child->address()->make());
    assertType('WpStarter\Types\Relations\Address', $child->address()->create([]));
    assertType('WpStarter\Types\Relations\Address', $child->address()->getRelated());
    assertType('WpStarter\Types\Relations\ChildUser', $child->address()->getParent());

    assertType('WpStarter\Database\Eloquent\Relations\HasMany<WpStarter\Types\Relations\Post, WpStarter\Types\Relations\User>', $user->posts());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Post>', $user->posts()->getResults());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Post>', $user->posts()->makeMany([]));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Post>', $user->posts()->createMany([]));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Post>', $user->posts()->createManyQuietly([]));
    assertType('WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Post, WpStarter\Types\Relations\User>', $user->latestPost());
    assertType('WpStarter\Types\Relations\Post', $user->posts()->make());
    assertType('WpStarter\Types\Relations\Post', $user->posts()->create());
    assertType('WpStarter\Types\Relations\Post|false', $user->posts()->save(new Post()));
    assertType('WpStarter\Types\Relations\Post|false', $user->posts()->saveQuietly(new Post()));

    assertType("WpStarter\Database\Eloquent\Relations\BelongsToMany<WpStarter\Types\Relations\Role, WpStarter\Types\Relations\User, WpStarter\Database\Eloquent\Relations\Pivot, 'pivot'>", $user->roles());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->getResults());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->find([1]));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->findMany([1, 2, 3]));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->findOrNew([1]));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->findOrFail([1]));
    assertType('42|WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->findOr([1], fn () => 42));
    assertType('42|WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->findOr([1], callback: fn () => 42));
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->findOrNew(1));
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->findOrFail(1));
    assertType('(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})|null', $user->roles()->find(1));
    assertType('42|(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})', $user->roles()->findOr(1, fn () => 42));
    assertType('42|(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})', $user->roles()->findOr(1, callback: fn () => 42));
    assertType('(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})|null', $user->roles()->first());
    assertType('42|(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})', $user->roles()->firstOr(fn () => 42));
    assertType('42|(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})', $user->roles()->firstOr(callback: fn () => 42));
    assertType('(WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot})|null', $user->roles()->firstWhere('foo'));
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->firstOrNew());
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->firstOrFail());
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->firstOrCreate());
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->create());
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->createOrFirst());
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->updateOrCreate([]));
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->save(new Role()));
    assertType('WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}', $user->roles()->saveQuietly(new Role()));
    $roles = $user->roles()->getResults();
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->saveMany($roles));
    assertType('array<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->saveMany($roles->all()));
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->saveManyQuietly($roles));
    assertType('array<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->saveManyQuietly($roles->all()));
    assertType('array<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->createMany($roles));
    assertType('array{attached: array, detached: array, updated: array}', $user->roles()->sync($roles));
    assertType('array{attached: array, detached: array, updated: array}', $user->roles()->syncWithoutDetaching($roles));
    assertType('array{attached: array, detached: array, updated: array}', $user->roles()->syncWithPivotValues($roles, []));
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->lazy());
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->lazyById());
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Role&object{pivot: WpStarter\Database\Eloquent\Relations\Pivot}>', $user->roles()->cursor());

    assertType('WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Types\Relations\Car, WpStarter\Types\Relations\Mechanic, WpStarter\Types\Relations\User>', $user->car());
    assertType('WpStarter\Types\Relations\Car|null', $user->car()->getResults());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Car>', $user->car()->find([1]));
    assertType('42|WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Car>', $user->car()->findOr([1], fn () => 42));
    assertType('42|WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Car>', $user->car()->findOr([1], callback: fn () => 42));
    assertType('WpStarter\Types\Relations\Car|null', $user->car()->find(1));
    assertType('42|WpStarter\Types\Relations\Car', $user->car()->findOr(1, fn () => 42));
    assertType('42|WpStarter\Types\Relations\Car', $user->car()->findOr(1, callback: fn () => 42));
    assertType('WpStarter\Types\Relations\Car|null', $user->car()->first());
    assertType('42|WpStarter\Types\Relations\Car', $user->car()->firstOr(fn () => 42));
    assertType('42|WpStarter\Types\Relations\Car', $user->car()->firstOr(callback: fn () => 42));
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Car>', $user->car()->lazy());
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Car>', $user->car()->lazyById());
    assertType('WpStarter\Support\LazyCollection<int, WpStarter\Types\Relations\Car>', $user->car()->cursor());

    assertType('WpStarter\Database\Eloquent\Relations\HasManyThrough<WpStarter\Types\Relations\Part, WpStarter\Types\Relations\Mechanic, WpStarter\Types\Relations\User>', $user->parts());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Part>', $user->parts()->getResults());
    assertType('WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Types\Relations\Part, WpStarter\Types\Relations\Mechanic, WpStarter\Types\Relations\User>', $user->firstPart());

    assertType('WpStarter\Database\Eloquent\Relations\BelongsTo<WpStarter\Types\Relations\User, WpStarter\Types\Relations\Post>', $post->user());
    assertType('WpStarter\Types\Relations\User|null', $post->user()->getResults());
    assertType('WpStarter\Types\Relations\User', $post->user()->make());
    assertType('WpStarter\Types\Relations\User', $post->user()->create());
    assertType('WpStarter\Types\Relations\Post', $post->user()->associate(new User()));
    assertType('WpStarter\Types\Relations\Post', $post->user()->dissociate());
    assertType('WpStarter\Types\Relations\Post', $post->user()->disassociate());
    assertType('WpStarter\Types\Relations\Post', $post->user()->getChild());

    assertType('WpStarter\Database\Eloquent\Relations\MorphOne<WpStarter\Types\Relations\Image, WpStarter\Types\Relations\Post>', $post->image());
    assertType('WpStarter\Types\Relations\Image|null', $post->image()->getResults());
    assertType('WpStarter\Types\Relations\Image', $post->image()->forceCreate([]));

    assertType('WpStarter\Database\Eloquent\Relations\MorphMany<WpStarter\Types\Relations\Comment, WpStarter\Types\Relations\Post>', $post->comments());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Comment>', $post->comments()->getResults());
    assertType('WpStarter\Database\Eloquent\Relations\MorphOne<WpStarter\Types\Relations\Comment, WpStarter\Types\Relations\Post>', $post->latestComment());

    assertType('WpStarter\Database\Eloquent\Relations\MorphTo<WpStarter\Database\Eloquent\Model, WpStarter\Types\Relations\Comment>', $comment->commentable());
    assertType('WpStarter\Database\Eloquent\Model|null', $comment->commentable()->getResults());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Comment>', $comment->commentable()->getEager());
    assertType('WpStarter\Database\Eloquent\Model', $comment->commentable()->createModelByType('foo'));
    assertType('WpStarter\Types\Relations\Comment', $comment->commentable()->associate(new Post()));
    assertType('WpStarter\Types\Relations\Comment', $comment->commentable()->dissociate());

    assertType("WpStarter\Database\Eloquent\Relations\MorphToMany<WpStarter\Types\Relations\Tag, WpStarter\Types\Relations\Post, WpStarter\Database\Eloquent\Relations\MorphPivot, 'pivot'>", $post->tags());
    assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Types\Relations\Tag&object{pivot: WpStarter\Database\Eloquent\Relations\MorphPivot}>', $post->tags()->getResults());

    assertType('42', Relation::noConstraints(fn () => 42));
}

class User extends Model
{
    /** @return HasOne<Address, $this> */
    public function address(): HasOne
    {
        $hasOne = $this->hasOne(Address::class);
        assertType('WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Address, $this(WpStarter\Types\Relations\User)>', $hasOne);

        return $hasOne;
    }

    /** @return HasMany<Post, $this> */
    public function posts(): HasMany
    {
        $hasMany = $this->hasMany(Post::class);
        assertType('WpStarter\Database\Eloquent\Relations\HasMany<WpStarter\Types\Relations\Post, $this(WpStarter\Types\Relations\User)>', $hasMany);

        return $hasMany;
    }

    /** @return HasOne<Post, $this> */
    public function latestPost(): HasOne
    {
        $post = $this->posts()->one();
        assertType('WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Post, $this(WpStarter\Types\Relations\User)>', $post);

        return $post;
    }

    /** @return BelongsToMany<Role, $this> */
    public function roles(): BelongsToMany
    {
        $belongsToMany = $this->belongsToMany(Role::class);
        assertType('WpStarter\Database\Eloquent\Relations\BelongsToMany<WpStarter\Types\Relations\Role, $this(WpStarter\Types\Relations\User), WpStarter\Database\Eloquent\Relations\Pivot, \'pivot\'>', $belongsToMany);

        return $belongsToMany;
    }

    /** @return HasOne<Mechanic, $this> */
    public function mechanic(): HasOne
    {
        return $this->hasOne(Mechanic::class);
    }

    /** @return HasMany<Mechanic, $this> */
    public function mechanics(): HasMany
    {
        return $this->hasMany(Mechanic::class);
    }

    /** @return HasOneThrough<Car, Mechanic, $this> */
    public function car(): HasOneThrough
    {
        $hasOneThrough = $this->hasOneThrough(Car::class, Mechanic::class);
        assertType('WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Types\Relations\Car, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>', $hasOneThrough);

        $through = $this->through('mechanic');
        assertType(
            'WpStarter\Database\Eloquent\PendingHasThroughRelationship<WpStarter\Database\Eloquent\Model, $this(WpStarter\Types\Relations\User)>',
            $through,
        );
        assertType(
            'WpStarter\Database\Eloquent\Relations\HasManyThrough<WpStarter\Database\Eloquent\Model, WpStarter\Database\Eloquent\Model, $this(WpStarter\Types\Relations\User)>|WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Database\Eloquent\Model, WpStarter\Database\Eloquent\Model, $this(WpStarter\Types\Relations\User)>',
            $through->has('car'),
        );

        $through = $this->through($this->mechanic());
        assertType(
            'WpStarter\Database\Eloquent\PendingHasThroughRelationship<WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User), WpStarter\Database\Eloquent\Relations\HasOne<WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>>',
            $through,
        );
        assertType(
            'WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Types\Relations\Car, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>',
            $through->has(function ($mechanic) {
                assertType('WpStarter\Types\Relations\Mechanic', $mechanic);

                return $mechanic->car();
            }),
        );

        return $hasOneThrough;
    }

    /** @return HasManyThrough<Car, Mechanic, $this> */
    public function cars(): HasManyThrough
    {
        $through = $this->through($this->mechanics());
        assertType(
            'WpStarter\Database\Eloquent\PendingHasThroughRelationship<WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User), WpStarter\Database\Eloquent\Relations\HasMany<WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>>',
            $through,
        );
        $hasManyThrough = $through->has(function ($mechanic) {
            assertType('WpStarter\Types\Relations\Mechanic', $mechanic);

            return $mechanic->car();
        });
        assertType(
            'WpStarter\Database\Eloquent\Relations\HasManyThrough<WpStarter\Types\Relations\Car, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>',
            $hasManyThrough,
        );

        return $hasManyThrough;
    }

    /** @return HasManyThrough<Part, Mechanic, $this> */
    public function parts(): HasManyThrough
    {
        $hasManyThrough = $this->hasManyThrough(Part::class, Mechanic::class);
        assertType('WpStarter\Database\Eloquent\Relations\HasManyThrough<WpStarter\Types\Relations\Part, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>', $hasManyThrough);

        assertType(
            'WpStarter\Database\Eloquent\Relations\HasManyThrough<WpStarter\Types\Relations\Part, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>',
            $this->through($this->mechanic())->has(fn ($mechanic) => $mechanic->parts()),
        );

        return $hasManyThrough;
    }

    /** @return HasOneThrough<Part, Mechanic, $this> */
    public function firstPart(): HasOneThrough
    {
        $part = $this->parts()->one();
        assertType('WpStarter\Database\Eloquent\Relations\HasOneThrough<WpStarter\Types\Relations\Part, WpStarter\Types\Relations\Mechanic, $this(WpStarter\Types\Relations\User)>', $part);

        return $part;
    }
}

class Post extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        $belongsTo = $this->belongsTo(User::class);
        assertType('WpStarter\Database\Eloquent\Relations\BelongsTo<WpStarter\Types\Relations\User, $this(WpStarter\Types\Relations\Post)>', $belongsTo);

        return $belongsTo;
    }

    /** @return MorphOne<Image, $this> */
    public function image(): MorphOne
    {
        $morphOne = $this->morphOne(Image::class, 'imageable');
        assertType('WpStarter\Database\Eloquent\Relations\MorphOne<WpStarter\Types\Relations\Image, $this(WpStarter\Types\Relations\Post)>', $morphOne);

        return $morphOne;
    }

    /** @return MorphMany<Comment, $this> */
    public function comments(): MorphMany
    {
        $morphMany = $this->morphMany(Comment::class, 'commentable');
        assertType('WpStarter\Database\Eloquent\Relations\MorphMany<WpStarter\Types\Relations\Comment, $this(WpStarter\Types\Relations\Post)>', $morphMany);

        return $morphMany;
    }

    /** @return MorphOne<Comment, $this> */
    public function latestComment(): MorphOne
    {
        $comment = $this->comments()->one();
        assertType('WpStarter\Database\Eloquent\Relations\MorphOne<WpStarter\Types\Relations\Comment, $this(WpStarter\Types\Relations\Post)>', $comment);

        return $comment;
    }

    /** @return MorphToMany<Tag, $this> */
    public function tags(): MorphToMany
    {
        $morphToMany = $this->morphedByMany(Tag::class, 'taggable');
        assertType('WpStarter\Database\Eloquent\Relations\MorphToMany<WpStarter\Types\Relations\Tag, $this(WpStarter\Types\Relations\Post), WpStarter\Database\Eloquent\Relations\MorphPivot, \'pivot\'>', $morphToMany);

        return $morphToMany;
    }
}

class Comment extends Model
{
    /** @return MorphTo<\WpStarter\Database\Eloquent\Model, $this> */
    public function commentable(): MorphTo
    {
        $morphTo = $this->morphTo();
        assertType('WpStarter\Database\Eloquent\Relations\MorphTo<WpStarter\Database\Eloquent\Model, $this(WpStarter\Types\Relations\Comment)>', $morphTo);

        return $morphTo;
    }
}

class Tag extends Model
{
    /** @return MorphToMany<Post, $this> */
    public function posts(): MorphToMany
    {
        $morphToMany = $this->morphToMany(Post::class, 'taggable');
        assertType('WpStarter\Database\Eloquent\Relations\MorphToMany<WpStarter\Types\Relations\Post, $this(WpStarter\Types\Relations\Tag), WpStarter\Database\Eloquent\Relations\MorphPivot, \'pivot\'>', $morphToMany);

        return $morphToMany;
    }
}

class Mechanic extends Model
{
    /** @return HasOne<Car, $this> */
    public function car(): HasOne
    {
        return $this->hasOne(Car::class);
    }

    /** @return HasMany<Part, $this> */
    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }
}

class ChildUser extends User
{
}
class Address extends Model
{
}
class Role extends Model
{
}
class Car extends Model
{
}
class Part extends Model
{
}
class Image extends Model
{
}
