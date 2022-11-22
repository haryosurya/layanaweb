<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToThemeSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theme_sections', function (Blueprint $table) {
            $table->foreign(['ad_id'])->references(['id'])->on('ads')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme_sections', function (Blueprint $table) {
            $table->dropForeign('theme_sections_ad_id_foreign');
            $table->dropForeign('theme_sections_category_id_foreign');
        });
    }
}
