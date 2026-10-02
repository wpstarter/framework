<?php

namespace WpStarter\Tests\Integration\Generators;

use WpStarter\Queue\Console\BatchesTableCommand;

class QueueBatchesTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan(BatchesTableCommand::class)->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use WpStarter\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'job_batches\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'job_batches\');',
        ], 'create_job_batches_table.php');
    }
}
