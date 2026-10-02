<?php

namespace WpStarter\Auth;

use Closure;
use WpStarter\Contracts\Auth\Authenticatable as UserContract;
use WpStarter\Contracts\Auth\UserProvider;
use WpStarter\Contracts\Hashing\Hasher as HasherContract;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Database\ConnectionInterface;

class DatabaseUserProvider implements UserProvider
{
    /**
     * The active database connection.
     *
     * @var \WpStarter\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The hasher implementation.
     *
     * @var \WpStarter\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The table containing the users.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new database user provider.
     *
     * @param  \WpStarter\Database\ConnectionInterface  $connection
     * @param  \WpStarter\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \WpStarter\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = $this->connection->table($this->table)->find($identifier);

        return $this->getGenericUser($user);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \WpStarter\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        $user = $this->getGenericUser(
            $this->connection->table($this->table)->find($identifier)
        );

        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user
            : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, #[\SensitiveParameter] $token)
    {
        $this->connection->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getRememberTokenName() => $token]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \WpStarter\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->connection->table($this->table);

        foreach ($credentials as $key => $value) {
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
            } else {
                $query->where($key, $value);
            }
        }

        // Now we are ready to execute the query to see if we have a user matching
        // the given credentials. If not, we will just return null and indicate
        // that there are no matching users from the given credential arrays.
        $user = $query->first();

        return $this->getGenericUser($user);
    }

    /**
     * Get the generic user.
     *
     * @param  mixed  $user
     * @return \WpStarter\Auth\GenericUser|null
     */
    protected function getGenericUser($user)
    {
        if (! is_null($user)) {
            return new GenericUser((array) $user);
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials)
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        if (is_null($hashed = $user->getAuthPassword())) {
            return false;
        }

        return $this->hasher->check($plain, $hashed);
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(UserContract $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        if (! $this->hasher->needsRehash($user->getAuthPassword()) && ! $force) {
            return;
        }

        $this->connection->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getAuthPasswordName() => $this->hasher->make($credentials['password'])]);
    }
}
