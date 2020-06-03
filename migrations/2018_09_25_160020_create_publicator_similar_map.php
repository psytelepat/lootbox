<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorSimilarMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicator_similar_map', function (Blueprint $table) {
            $table->unsignedInteger('lng')->index();
            $table->unsignedInteger('post_grp')->index();
            $table->unsignedInteger('similar_grp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publicator_similar_map');
    }
}
