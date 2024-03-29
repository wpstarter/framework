<?php

namespace WpStarter\Tests\Filesystem;

use WpStarter\Contracts\Filesystem\Filesystem;
use WpStarter\Filesystem\FilesystemManager;
use WpStarter\Foundation\Application;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FilesystemManagerTest extends TestCase
{
    public function testExceptionThrownOnUnsupportedDriver()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Disk [local] does not have a configured driver.');

        $filesystem = new FilesystemManager(ws_tap(new Application, function ($app) {
            $app['config'] = ['filesystems.disks.local' => null];
        }));

        $filesystem->disk('local');
    }

    public function testCanBuildOnDemandDisk()
    {
        $filesystem = new FilesystemManager(new Application);

        $this->assertInstanceOf(Filesystem::class, $filesystem->build('my-custom-path'));

        $this->assertInstanceOf(Filesystem::class, $filesystem->build([
            'driver' => 'local',
            'root' => 'my-custom-path',
            'url' => 'my-custom-url',
            'visibility' => 'public',
        ]));

        rmdir(__DIR__.'/../../my-custom-path');
    }
}
