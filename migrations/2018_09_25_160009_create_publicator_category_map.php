<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicatorCategoryMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicator_category_map', function (Blueprint $table) {
            $table->unsignedInteger('lng')->index();
            $table->unsignedInteger('post_grp')->index();
            $table->unsignedInteger('category_grp')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publicator_category_map');
    }
}
