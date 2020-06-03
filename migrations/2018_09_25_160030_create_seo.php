<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('trg');
            $table->unsignedInteger('usr');
            $table->unsignedInteger('grp');
            $table->unsignedInteger('lng')->default(1);

            $table->text('title');
            $table->text('description');
            $table->text('keywords');

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
        Schema::drop('seo');
    }
}
