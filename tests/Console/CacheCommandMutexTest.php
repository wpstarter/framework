<?php

namespace WpStarter\Tests\Console;

use WpStarter\Console\CacheCommandMutex;
use WpStarter\Console\Command;
use WpStarter\Contracts\Cache\Factory;
use WpStarter\Contracts\Cache\LockProvider;
use WpStarter\Contracts\Cache\Repository;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class CacheCommandMutexTest extends TestCase
{
    /**
     * @var \WpStarter\Console\CacheCommandMutex
     */
    protected $mutex;

    /**
     * @var \WpStarter\Console\Command
     */
    protected $command;

    /**
     * @var \WpStarter\Contracts\Cache\Factory
     */
    protected $cacheFactory;

    /**
     * @var \WpStarter\Contracts\Cache\Repository
     */
    protected $cacheRepository;

    protected function setUp(): void
    {
        $this->cacheFactory = m::mock(Factory::class);
        $this->cacheRepository = m::mock(Repository::class);
        $this->mutex = new CacheCommandMutex($this->cacheFactory);
        $this->command = new class extends Command
        {
            protected $name = 'command-name';
        };
    }

    public function testCanCreateMutex()
    {
        $this->mockUsingCacheStore();
        $this->cacheRepository->shouldReceive('add')
            ->andReturn(true)
            ->once();
        $actual = $this->mutex->create($this->command);

        $this->assertTrue($actual);
    }

    public function testCannotCreateMutexIfAlreadyExist()
    {
        $this->mockUsingCacheStore();
        $this->cacheRepository->shouldReceive('add')
            ->andReturn(false)
            ->once();
        $actual = $this->mutex->create($this->command);

        $this->assertFalse($actual);
    }

    public function testCanCreateMutexWithCustomConnection()
    {
        $this->mockUsingCacheStore();
        $this->cacheRepository->shouldReceive('getStore')
            ->with('test')
            ->andReturn($this->cacheRepository);
        $this->cacheRepository->shouldReceive('add')
            ->andReturn(false)
            ->once();
        $this->mutex->useStore('test');

        $this->mutex->create($this->command);
    }

    public function testCanCreateMutexWithLockProvider()
    {
        $lock = $this->mockUsingLockProvider();
        $this->acquireLockExpectations($lock, true);

        $actual = $this->mutex->create($this->command);

        $this->assertTrue($actual);
    }

    public function testCanCreateMutexWithCustomLockProviderConnection()
    {
        $this->mockUsingCacheStore();
        $this->cacheRepository->shouldReceive('getStore')
            ->with('test')
            ->andReturn($this->cacheRepository);
        $this->cacheRepository->shouldReceive('add')
            ->andReturn(false)
            ->once();
        $this->mutex->useStore('test');

        $this->mutex->create($this->command);
    }

    public function testCannotCreateMutexIfAlreadyExistWithLockProvider()
    {
        $lock = $this->mockUsingLockProvider();
        $this->acquireLockExpectations($lock, false);
        $actual = $this->mutex->create($this->command);

        $this->assertFalse($actual);
    }

    public function testCanCreateMutexWithCustomConnectionWithLockProvider()
    {
        $lock = m::mock(LockProvider::class);
        $this->cacheFactory->expects('store')->once()->with('test')->andReturn($this->cacheRepository);
        $this->cacheRepository->expects('getStore')->twice()->andReturn($lock);

        $this->acquireLockExpectations($lock, true);
        $this->mutex->useStore('test');

        $this->mutex->create($this->command);
    }

    /**
     * @return void
     */
    private function mockUsingCacheStore(): void
    {
        $this->cacheFactory->expects('store')->once()->andReturn($this->cacheRepository);
        $this->cacheRepository->expects('getStore')->andReturn(null);
    }

    private function mockUsingLockProvider(): m\MockInterface
    {
        $lock = m::mock(LockProvider::class);
        $this->cacheFactory->expects('store')->once()->andReturn($this->cacheRepository);
        $this->cacheRepository->expects('getStore')->twice()->andReturn($lock);

        return $lock;
    }

    private function acquireLockExpectations(MockInterface $lock, bool $acquiresSuccessfully): void
    {
        $lock->expects('lock')
            ->once()
            ->with(m::type('string'), m::type('int'))
            ->andReturns($lock);

        $lock->expects('get')
            ->once()
            ->andReturns($acquiresSuccessfully);
    }

    public function testCommandMutexNameWithoutIsolatedMutexNameMethod()
    {
        $this->mockUsingCacheStore();

        $this->cacheRepository->shouldReceive('getStore')
            ->with('test')
            ->andReturn($this->cacheRepository);

        $this->cacheRepository->shouldReceive('add')
            ->once()
            ->withArgs(function ($key) {
                $this->assertEquals('framework'.DIRECTORY_SEPARATOR.'command-command-name', $key);

                return true;
            })
            ->andReturn(true);

        $this->mutex->create($this->command);
    }

    public function testCommandMutexNameWithIsolatedMutexNameMethod()
    {
        $command = new class extends Command
        {
            protected $name = 'command-name';

            public function isolatableId()
            {
                return 'isolated';
            }
        };

        $this->mockUsingCacheStore();

        $this->cacheRepository->shouldReceive('getStore')
            ->with('test')
            ->andReturn($this->cacheRepository);

        $this->cacheRepository->shouldReceive('add')
            ->once()
            ->withArgs(function ($key) {
                $this->assertEquals('framework'.DIRECTORY_SEPARATOR.'command-command-name-isolated', $key);

                return true;
            })
            ->andReturn(true);

        $this->mutex->create($command);
    }
}
