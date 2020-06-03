<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicator_post', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('trg')->default(1);
            $table->unsignedInteger('usr');
            $table->unsignedInteger('grp');
            $table->unsignedInteger('lng')->default(1);
            $table->unsignedInteger('lnk')->default(0);
            $table->unsignedInteger('pos')->default(0);

            $table->string('slug');
            $table->string('title');

            $table->text('description')->nullable();
            $table->text('content')->nullable();

            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('author_id')->default(0);
            $table->boolean('is_published')->default(0);

            $table->unsignedInteger('views')->nullable()->default(0);
            $table->float('rating')->nullable()->default(0);

            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('noindex')->nullable()->default(0);
            $table->unsignedInteger('alpha_index')->nullable();
            $table->unsignedInteger('rectype')->nullable()->default(0);
            $table->text('video_code')->nullable();

            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

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
        Schema::drop('publicator_post');
    }
}
