<?php

use WpStarter\Database\Migrations\Migration;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\DB;
use WpStarter\Support\Facades\Schema;

class CreatePeopleNonDynamicTable extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('people')->insert([
            ['email' => 'jane@example.com', 'name' => 'Jane Doe', 'password' => 'secret'],
            ['email' => 'john@example.com', 'name' => 'John Doe', 'password' => 'secret'],
        ]);
    }
}
