<?php

namespace WpStarter\Tests\Integration\Queue;

use WpStarter\Bus\Queueable;
use WpStarter\Contracts\Encryption\DecryptException;
use WpStarter\Contracts\Queue\ShouldBeEncrypted;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Foundation\Bus\Dispatchable;
use WpStarter\Foundation\Testing\DatabaseMigrations;
use WpStarter\Support\Facades\Bus;
use WpStarter\Support\Facades\DB;
use WpStarter\Support\Facades\Queue;
use WpStarter\Support\Str;
use WpStarter\Tests\Integration\Database\DatabaseTestCase;
use Orchestra\Testbench\Attributes\WithMigration;

#[WithMigration]
#[WithMigration('queue')]
class JobEncryptionTest extends DatabaseTestCase
{
    use DatabaseMigrations;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('queue.default', 'database');
    }

    protected function tearDown(): void
    {
        JobEncryptionTestEncryptedJob::$ran = false;
        JobEncryptionTestNonEncryptedJob::$ran = false;

        parent::tearDown();
    }

    public function testEncryptedJobPayloadIsStoredEncrypted()
    {
        Bus::dispatch(new JobEncryptionTestEncryptedJob);

        $this->assertNotEmpty(
            ws_decrypt(json_decode(DB::table('jobs')->first()->payload)->data->command)
        );
    }

    public function testNonEncryptedJobPayloadIsStoredRaw()
    {
        Bus::dispatch(new JobEncryptionTestNonEncryptedJob);

        $this->expectException(DecryptException::class);
        $this->expectExceptionMessage('The payload is invalid');

        $this->assertInstanceOf(JobEncryptionTestNonEncryptedJob::class,
            unserialize(json_decode(DB::table('jobs')->first()->payload)->data->command)
        );

        ws_decrypt(json_decode(DB::table('jobs')->first()->payload)->data->command);
    }

    public function testQueueCanProcessEncryptedJob()
    {
        Bus::dispatch(new JobEncryptionTestEncryptedJob);

        Queue::pop()->fire();

        $this->assertTrue(JobEncryptionTestEncryptedJob::$ran);
    }

    public function testQueueCanProcessUnEncryptedJob()
    {
        Bus::dispatch(new JobEncryptionTestNonEncryptedJob);

        Queue::pop()->fire();

        $this->assertTrue(JobEncryptionTestNonEncryptedJob::$ran);
    }
}

class JobEncryptionTestEncryptedJob implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable, Queueable;

    public static $ran = false;

    public function handle()
    {
        static::$ran = true;
    }
}

class JobEncryptionTestNonEncryptedJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public static $ran = false;

    public function handle()
    {
        static::$ran = true;
    }
}
