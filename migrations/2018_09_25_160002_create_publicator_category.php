<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicator_category', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('trg')->default(1);
            $table->unsignedInteger('usr')->default(0);
            $table->unsignedInteger('grp');
            $table->unsignedInteger('lng')->default(1);
            $table->unsignedInteger('lnk')->default(0)->nullable();
            $table->unsignedInteger('pos');

            $table->string('slug')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();

            $table->unsignedInteger('usage')->default(0);

            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->boolean('is_published');

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
        Schema::drop('publicator_category');
    }
}
