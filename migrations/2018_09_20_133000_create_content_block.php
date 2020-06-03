<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_block', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('trg')->index();
            $table->integer('usr')->index();
            $table->integer('grp')->index();
            $table->integer('mid')->default(0);
            $table->integer('lng')->default(1);
            $table->integer('lnk')->default(0);
            $table->integer('pos')->default(0);
            $table->integer('dsp')->default(0);

            $table->integer('mode')->default(1);

            $table->string('slug')->nullable();
            $table->string('title')->nullable();

            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('code')->nullable();

            $table->integer('align')->default(0)->nullable();
            $table->integer('style')->default(0)->nullable();

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
        Schema::drop('content_block');
    }
}
