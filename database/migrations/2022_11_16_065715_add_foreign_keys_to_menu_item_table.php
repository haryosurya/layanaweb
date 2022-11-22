<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMenuItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_item', function (Blueprint $table) {
            $table->foreign(['parent'])->references(['id'])->on('menu_item')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['sub_category_id'])->references(['id'])->on('sub_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['page_id'])->references(['id'])->on('pages')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['post_id'])->references(['id'])->on('posts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['menu_id'])->references(['id'])->on('menu')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item', function (Blueprint $table) {
            $table->dropForeign('menu_item_parent_foreign');
            $table->dropForeign('menu_item_sub_category_id_foreign');
            $table->dropForeign('menu_item_category_id_foreign');
            $table->dropForeign('menu_item_page_id_foreign');
            $table->dropForeign('menu_item_post_id_foreign');
            $table->dropForeign('menu_item_menu_id_foreign');
        });
    }
}
