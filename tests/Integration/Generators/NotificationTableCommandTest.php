<?php

namespace WpStarter\Tests\Integration\Generators;

use WpStarter\Notifications\Console\NotificationTableCommand;

class NotificationTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan(NotificationTableCommand::class)->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use WpStarter\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'notifications\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'notifications\');',
        ], 'create_notifications_table.php');
    }
}
