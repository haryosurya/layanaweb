<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->foreign(['ad_id'])->references(['id'])->on('ads')->onUpdate('CASCADE')->onDelete('CASCADE');
            // $table->foreign(['poll_id'])->references(['id'])->on('polls')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropForeign('widgets_ad_id_foreign');
            $table->dropForeign('widgets_poll_id_foreign');
        });
    }
}
