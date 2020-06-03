<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvatar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avatar', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('usr')->nullable()->index();
            $table->integer('grp')->nullable()->index();
            $table->integer('trg')->nullable()->index();
            $table->integer('lng')->nullable()->index();

            $table->integer('lnk')->nullable()->index();
            $table->integer('pos')->nullable()->default(0);

            $table->integer('size')->index();
            $table->integer('type')->default(0);

            $table->string('fn');
            $table->integer('w')->default(0);
            $table->integer('h')->default(0);
            $table->integer('fs')->default(0);

            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('href')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('avatar');
    }
}
