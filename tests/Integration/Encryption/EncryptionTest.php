<?php

namespace WpStarter\Tests\Integration\Encryption;

use WpStarter\Encryption\Encrypter;
use WpStarter\Encryption\EncryptionServiceProvider;
use Orchestra\Testbench\TestCase;
use RuntimeException;

class EncryptionTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:IUHRqAQ99pZ0A1MPjbuv1D6ff3jxv0GIvS2qIW4JNU4=');
    }

    protected function getPackageProviders($app)
    {
        return [EncryptionServiceProvider::class];
    }

    public function testEncryptionProviderBind()
    {
        self::assertInstanceOf(Encrypter::class, $this->app->make('encrypter'));
    }

    public function testEncryptionWillNotBeInstantiableWhenMissingAppKey()
    {
        $this->expectException(RuntimeException::class);

        $this->app['config']->set('app.key', null);

        $this->app->make('encrypter');
    }
}
