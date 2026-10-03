<?php

declare(strict_types=1);

namespace WpStarter\Tests\Integration\Generators;

class TraitMakeCommandTest extends TestCase
{
    public function testItCanGenerateTraitFile()
    {
        $this->artisan('make:trait', ['name' => 'FooTrait'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App;',
            'trait FooTrait',
        ], 'app/FooTrait.php');
    }

    public function testItCanGenerateTraitFileWhenTraitsFolderExists()
    {
        $traitsFolderPath = ws_app_path('Traits');

        /** @var \WpStarter\Filesystem\Filesystem $files */
        $files = $this->app['files'];

        $files->ensureDirectoryExists($traitsFolderPath);

        $this->artisan('make:trait', ['name' => 'FooTrait'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Traits;',
            'trait FooTrait',
        ], 'app/Traits/FooTrait.php');

        $files->deleteDirectory($traitsFolderPath);
    }

    public function testItCanGenerateTraitFileWhenConcernsFolderExists()
    {
        $traitsFolderPath = ws_app_path('Concerns');

        /** @var \WpStarter\Filesystem\Filesystem $files */
        $files = $this->app['files'];

        $files->ensureDirectoryExists($traitsFolderPath);

        $this->artisan('make:trait', ['name' => 'FooTrait'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Concerns;',
            'trait FooTrait',
        ], 'app/Concerns/FooTrait.php');

        $files->deleteDirectory($traitsFolderPath);
    }
}
