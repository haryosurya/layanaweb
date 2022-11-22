<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMenuLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_locations', function (Blueprint $table) {
            $table->foreign(['menu_id'])->references(['id'])->on('menu')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_locations', function (Blueprint $table) {
            $table->dropForeign('menu_locations_menu_id_foreign');
        });
    }
}
