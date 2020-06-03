<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentBlockQuoteImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_block_quote_image', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('usr')->nullable()->index();
            $table->unsignedInteger('grp')->nullable()->index();
            $table->unsignedInteger('trg')->nullable()->index();
            $table->unsignedInteger('lng')->default(1)->index();

            $table->unsignedInteger('lnk')->nullable()->index();
            $table->unsignedInteger('pos')->nullable()->default(0);

            $table->unsignedInteger('size')->index();
            $table->unsignedInteger('type')->default(0);

            $table->string('fn');
            $table->unsignedInteger('w')->default(0);
            $table->unsignedInteger('h')->default(0);
            $table->unsignedInteger('fs')->default(0);

            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
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
        Schema::drop('content_block_quote_image');
    }
}
