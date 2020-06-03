<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorAuthorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicator_author', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('usr')->nullable()->index();
            $table->unsignedInteger('grp')->nullable()->index();
            $table->unsignedInteger('trg')->nullable()->index();
            $table->unsignedInteger('lng')->nullable()->index();

            $table->unsignedInteger('lnk')->nullable()->index();
            $table->unsignedInteger('pos')->nullable()->index();

            $table->string('slug')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('content')->nullable();

            $table->string('ig_url')->nullable();
            $table->string('tw_url')->nullable();
            $table->string('fb_url')->nullable();
            $table->string('vk_url')->nullable();
            $table->string('yt_url')->nullable();

            $table->longText('payload')->nullable();

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
        Schema::dropIfExists('publicator_author');
    }
}
