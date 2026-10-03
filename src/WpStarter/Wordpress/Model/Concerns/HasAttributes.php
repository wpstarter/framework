<?php

namespace WpStarter\Wordpress\Model\Concerns;

use BackedEnum;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use Stringable;
use ValueError;
use WpStarter\Contracts\Database\Eloquent\Castable;
use WpStarter\Contracts\Database\Eloquent\CastsInboundAttributes;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Database\Eloquent\Casts\AsArrayObject;
use WpStarter\Database\Eloquent\Casts\AsCollection;
use WpStarter\Database\Eloquent\Casts\AsEncryptedArrayObject;
use WpStarter\Database\Eloquent\Casts\AsEncryptedCollection;
use WpStarter\Database\Eloquent\Casts\AsEnumArrayObject;
use WpStarter\Database\Eloquent\Casts\AsEnumCollection;
use WpStarter\Database\Eloquent\Casts\Attribute;
use WpStarter\Database\Eloquent\InvalidCastException;
use WpStarter\Database\Eloquent\JsonEncodingException;
use WpStarter\Database\Eloquent\MissingAttributeException;
use WpStarter\Database\Eloquent\Relations\Relation;
use WpStarter\Database\LazyLoadingViolationException;
use WpStarter\Support\Arr;
use WpStarter\Support\Carbon;
use WpStarter\Support\Collection;
use WpStarter\Support\Collection as BaseCollection;
use WpStarter\Support\Facades\Crypt;
use WpStarter\Support\Facades\Date;
use WpStarter\Support\Facades\Hash;
use WpStarter\Support\Str;

use function WpStarter\Support\enum_value;

trait HasAttributes
{
    /**
     * The original WordPress user data snapshot.
     *
     * @var \stdClass|null
     */
    protected $originalData;

    /**
     * Determine whether an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        if (! $key) {
            return false;
        }

        return array_key_exists($key, (array) $this->data) ||
            array_key_exists($key, $this->casts) ||
            $this->hasGetMutator($key) ||
            $this->hasAttributeMutator($key) ||
            $this->isClassCastable($key);
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        $this->mergeAttributeFromCachedCasts($key);

        return $this->data->{$key} ?? null;
    }

    /**
     * Get the value of an "Attribute" return type marked attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttributeMarkedAttribute($key, $value)
    {
        if (array_key_exists($key, $this->attributeCastCache)) {
            return $this->attributeCastCache[$key];
        }

        $this->mergeAttributesFromCachedCasts();

        $attribute = $this->{Str::camel($key)}();

        $value = call_user_func($attribute->get ?: function ($value) {
            return $value;
        }, $value, (array) $this->data);

        if ($attribute->withCaching || (is_object($value) && $attribute->withObjectCaching)) {
            $this->attributeCastCache[$key] = $value;
        } else {
            unset($this->attributeCastCache[$key]);
        }

        return $value;
    }

    /**
     * Cast the given attribute using a custom cast class.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function getClassCastableAttributeValue($key, $value)
    {
        $caster = $this->resolveCasterClass($key);

        $objectCachingDisabled = $caster->withoutObjectCaching ?? false;

        if (isset($this->classCastCache[$key]) && ! $objectCachingDisabled) {
            return $this->classCastCache[$key];
        } else {
            $value = $caster instanceof CastsInboundAttributes
                ? $value
                : $caster->get($this, $key, $value, (array) $this->data);

            if ($caster instanceof CastsInboundAttributes ||
                ! is_object($value) ||
                $objectCachingDisabled) {
                unset($this->classCastCache[$key]);
            } else {
                $this->classCastCache[$key] = $value;
            }

            return $value;
        }
    }

    /**
     * Increment or decrement the given attribute using the custom cast class.
     *
     * @param  string  $method
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function deviateClassCastableAttribute($method, $key, $value)
    {
        return $this->resolveCasterClass($key)->{$method}(
            $this, $key, $value, (array) $this->data
        );
    }

    /**
     * Serialize the given attribute using the custom cast class.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function serializeClassCastableAttribute($key, $value)
    {
        return $this->resolveCasterClass($key)->serialize(
            $this, $key, $value, (array) $this->data
        );
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // this model, such as "json_encoding" a listing of data for storage.
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        } elseif ($this->hasAttributeSetMutator($key)) {
            return $this->setAttributeMarkedMutatedAttributeValue($key, $value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        elseif (! is_null($value) && $this->isDateAttribute($key)) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isEnumCastable($key)) {
            $this->setEnumCastableAttribute($key, $value);

            return $this;
        }

        if ($this->isClassCastable($key)) {
            $this->setClassCastableAttribute($key, $value);

            return $this;
        }

        if (! is_null($value) && $this->isJsonCastable($key)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        // If this attribute contains a JSON ->, we'll set the proper value in the
        // attribute's underlying array. This takes care of properly nesting an
        // attribute in the array's value in the case of deeply nested items.
        if (str_contains($key, '->')) {
            return $this->fillJsonAttribute($key, $value);
        }

        if (! is_null($value) && $this->isEncryptedCastable($key)) {
            $value = $this->castAttributeAsEncryptedString($key, $value);
        }

        if (! is_null($value) && $this->hasCast($key, 'hashed')) {
            $value = $this->castAttributeAsHashedString($key, $value);
        }

        $this->data->{$key} = $value;

        return $this;
    }

    /**
     * Set the value of a "Attribute" return type marked attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function setAttributeMarkedMutatedAttributeValue($key, $value)
    {
        $this->mergeAttributesFromCachedCasts();

        $attribute = $this->{Str::camel($key)}();

        $callback = $attribute->set ?: function ($value) use ($key) {
            $this->data->{$key} = $value;
        };

        $this->data = (object) array_merge(
            (array) $this->data,
            $this->normalizeCastClassResponse(
                $key, $callback($value, (array) $this->data)
            )
        );

        if ($attribute->withCaching || (is_object($value) && $attribute->withObjectCaching)) {
            $this->attributeCastCache[$key] = $value;
        } else {
            unset($this->attributeCastCache[$key]);
        }

        return $this;
    }

    /**
     * Set a given JSON attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function fillJsonAttribute($key, $value)
    {
        [$key, $path] = explode('->', $key, 2);

        $value = $this->asJson($this->getArrayAttributeWithValue(
            $path, $key, $value
        ), $this->getJsonCastFlags($key));

        $this->data->{$key} = $this->isEncryptedCastable($key)
            ? $this->castAttributeAsEncryptedString($key, $value)
            : $value;

        if ($this->isClassCastable($key)) {
            unset($this->classCastCache[$key]);
        }

        return $this;
    }

    /**
     * Set the value of a class castable attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected function setClassCastableAttribute($key, $value)
    {
        $caster = $this->resolveCasterClass($key);

        $this->data = (object) array_replace(
            (array) $this->data,
            $this->normalizeCastClassResponse($key, $caster->set(
                $this, $key, $value, (array) $this->data
            ))
        );

        if ($caster instanceof CastsInboundAttributes ||
            ! is_object($value) ||
            ($caster->withoutObjectCaching ?? false)) {
            unset($this->classCastCache[$key]);
        } else {
            $this->classCastCache[$key] = $value;
        }
    }

    /**
     * Set the value of an enum castable attribute.
     *
     * @param  string  $key
     * @param  \UnitEnum|string|int|null  $value
     * @return void
     */
    protected function setEnumCastableAttribute($key, $value)
    {
        $enumClass = $this->getCasts()[$key];

        if (! isset($value)) {
            $this->data->{$key} = null;
        } elseif (is_object($value)) {
            $this->data->{$key} = $this->getStorableEnumValue($enumClass, $value);
        } else {
            $this->data->{$key} = $this->getStorableEnumValue(
                $enumClass, $this->getEnumCaseFromValue($enumClass, $value)
            );
        }
    }

    /**
     * Get an array attribute or return an empty array if it is not set.
     *
     * @param  string  $key
     * @return array
     */
    protected function getArrayAttributeByKey($key)
    {
        if (! isset($this->data->{$key})) {
            return [];
        }

        return $this->fromJson(
            $this->isEncryptedCastable($key)
                ? $this->fromEncryptedString($this->data->{$key})
                : $this->data->{$key}
        );
    }

    /**
     * Merge the cast class attribute back into the model.
     *
     * @return void
     */
    protected function mergeAttributeFromClassCasts(string $key): void
    {
        if (! isset($this->classCastCache[$key])) {
            return;
        }

        $value = $this->classCastCache[$key];

        $caster = $this->resolveCasterClass($key);

        $this->data = (object) array_merge(
            (array) $this->data,
            $caster instanceof CastsInboundAttributes
                ? [$key => $value]
                : $this->normalizeCastClassResponse($key, $caster->set($this, $key, $value, (array) $this->data))
        );
    }

    /**
     * Merge the cast class attribute back into the model.
     *
     * @return void
     */
    protected function mergeAttributeFromAttributeCasts(string $key): void
    {
        if (! isset($this->attributeCastCache[$key])) {
            return;
        }

        $value = $this->attributeCastCache[$key];

        $attribute = $this->{Str::camel($key)}();

        if ($attribute->get && ! $attribute->set) {
            return;
        }

        $callback = $attribute->set ?: function ($value) use ($key) {
            $this->data->{$key} = $value;
        };

        $this->data = (object) array_merge(
            (array) $this->data,
            $this->normalizeCastClassResponse(
                $key, $callback($value, (array) $this->data)
            )
        );
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array<string, mixed>
     */
    public function getAttributes()
    {
        $this->mergeAttributesFromCachedCasts();

        return (array) $this->data;
    }

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param  array  $attributes
     * @param  bool  $sync
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->data = (object) $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        $this->classCastCache = [];
        $this->attributeCastCache = [];

        return $this;
    }

    /**
     * Get the model's original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    public function getOriginal($key = null, $default = null)
    {
        return (new static)->setRawAttributes(
            (array) $this->originalData, $sync = true
        )->getOriginalWithoutRewindingModel($key, $default);
    }

    /**
     * Get the model's original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    protected function getOriginalWithoutRewindingModel($key = null, $default = null)
    {
        if ($key) {
            return $this->transformModelValue(
                $key, Arr::get((array) $this->originalData, $key, $default)
            );
        }

        return (new Collection((array) $this->originalData))
            ->mapWithKeys(fn ($value, $key) => [$key => $this->transformModelValue($key, $value)])
            ->all();
    }

    /**
     * Get the model's raw original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    public function getRawOriginal($key = null, $default = null)
    {
        return Arr::get((array) $this->originalData, $key, $default);
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->originalData = (object) $this->getAttributes();

        return $this;
    }

    /**
     * Sync multiple original attribute with their current values.
     *
     * @param  array<string>|string  $attributes
     * @return $this
     */
    public function syncOriginalAttributes($attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $modelAttributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            $this->originalData->{$attribute} = $modelAttributes[$attribute];
        }

        return $this;
    }

    /**
     * Discard attribute changes and reset the attributes to their original state.
     *
     * @return $this
     */
    public function discardChanges()
    {
        [$this->data, $this->changes, $this->previous] = [(object) (array) $this->originalData, [], []];

        $this->classCastCache = [];
        $this->attributeCastCache = [];

        return $this;
    }

    /**
     * Determine if the new and old values for a given key are equivalent.
     *
     * @param  string  $key
     * @return bool
     */
    public function originalIsEquivalent($key)
    {
        if (! array_key_exists($key, (array) $this->originalData)) {
            return false;
        }

        $attribute = Arr::get((array) $this->data, $key);
        $original = Arr::get((array) $this->originalData, $key);

        if ($attribute === $original) {
            return true;
        } elseif (is_null($attribute)) {
            return false;
        } elseif ($this->isDateAttribute($key) || $this->isDateCastableWithCustomFormat($key)) {
            return $this->fromDateTime($attribute) ===
                $this->fromDateTime($original);
        } elseif ($this->hasCast($key, ['object', 'collection'])) {
            return $this->fromJson($attribute) ===
                $this->fromJson($original);
        } elseif ($this->hasCast($key, ['real', 'float', 'double'])) {
            if ($original === null) {
                return false;
            }

            return abs($this->castAttribute($key, $attribute) - $this->castAttribute($key, $original)) < PHP_FLOAT_EPSILON * 4;
        } elseif ($this->isEncryptedCastable($key) && ! empty(static::currentEncrypter()->getPreviousKeys())) {
            return false;
        } elseif ($this->hasCast($key, static::$primitiveCastTypes)) {
            return $this->castAttribute($key, $attribute) ===
                $this->castAttribute($key, $original);
        } elseif ($this->isClassCastable($key) && Str::startsWith($this->getCasts()[$key], [AsArrayObject::class, AsCollection::class])) {
            return $this->fromJson($attribute) === $this->fromJson($original);
        } elseif ($this->isClassCastable($key) && Str::startsWith($this->getCasts()[$key], [AsEnumArrayObject::class, AsEnumCollection::class])) {
            return $this->fromJson($attribute) === $this->fromJson($original);
        } elseif ($this->isClassCastable($key) && $original !== null && Str::startsWith($this->getCasts()[$key], [AsEncryptedArrayObject::class, AsEncryptedCollection::class])) {
            if (empty(static::currentEncrypter()->getPreviousKeys())) {
                return $this->fromEncryptedString($attribute) === $this->fromEncryptedString($original);
            }

            return false;
        } elseif ($this->isClassComparable($key)) {
            return $this->compareClassCastableAttribute($key, $original, $attribute);
        }

        return is_numeric($attribute) && is_numeric($original)
            && strcmp((string) $attribute, (string) $original) === 0;
    }

    /**
     * Transform a raw model value using mutators, casts, etc.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transformModelValue($key, $value)
    {
        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        } elseif ($this->hasAttributeGetMutator($key)) {
            return $this->mutateAttributeMarkedAttribute($key, $value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependent upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            if (static::preventsAccessingMissingAttributes() &&
                ! array_key_exists($key, (array) $this->data) &&
                ($this->isEnumCastable($key) ||
                 in_array($this->getCastType($key), static::$primitiveCastTypes))) {
                $this->throwMissingAttributeExceptionIfApplicable($key);
            }

            return $this->castAttribute($key, $value);
        }

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        if ($value !== null
            && \in_array($key, $this->getDates(), false)) {
            return $this->asDateTime($value);
        }

        return $value;
    }
}
