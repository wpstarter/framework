<?php

namespace WpStarter\Tests\Integration\Auth;

use WpStarter\Database\QueryException;
use WpStarter\Foundation\Auth\User as FoundationUser;
use WpStarter\Support\Facades\Route;
use WpStarter\Support\Str;
use Orchestra\Testbench\TestCase;

/**
 * @requires extension pdo_mysql
 */
class ApiAuthenticationWithEloquentTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // Auth configuration
        $app['config']->set('auth.defaults.guard', 'api');
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('auth.guards.api', [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ]);

        // Database configuration
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'username' => 'root',
            'password' => 'invalid-credentials',
            'database' => 'forge',
            'prefix' => '',
        ]);
    }

    public function testAuthenticationViaApiWithEloquentUsingWrongDatabaseCredentialsShouldNotCauseInfiniteLoop()
    {
        Route::get('/auth', function () {
            return 'success';
        })->middleware('auth:api');

        $this->expectException(QueryException::class);

        $this->expectExceptionMessage("Access denied for user 'root'@");

        try {
            $this->withoutExceptionHandling()->get('/auth', ['Authorization' => 'Bearer whatever']);
        } catch (QueryException $e) {
            if (Str::startsWith($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                $this->markTestSkipped('MySQL instance required.');
            }

            throw $e;
        }
    }
}

class User extends FoundationUser
{
    //
}
