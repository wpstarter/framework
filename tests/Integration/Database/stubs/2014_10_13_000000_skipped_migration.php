<?php

use WpStarter\Database\Migrations\Migration;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;

return new class extends Migration
{
    public function shouldRun(): bool
    {
        return false;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('skipped_table', function (Blueprint $table) {
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('skipped_table');
    }
};
