<?php

use WpStarter\Database\Migrations\Migration;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;

class CreateFlightsTable extends Migration
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
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
