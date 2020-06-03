<?php

use Illuminate\Database\Migrations\Migration;

class AddPublicatorPostAuthor extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('publicator_post', function ($table) {
            if (!Schema::hasColumn('publicator_post', 'author_id')) {
                $table->unsignedInteger('author_id')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('publicator_post', 'author_id')) {
            Schema::table('publicator_post', function ($table) {
                $table->dropColumn('author_id');
            });
        }
    }
}
