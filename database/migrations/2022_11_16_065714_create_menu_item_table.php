<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label', 191);
            $table->string('language', 191);
            $table->unsignedBigInteger('menu_id')->index('menu_item_menu_id_foreign');
            $table->enum('is_mega_menu', ['no', 'tab', 'category'])->comment('no = normal menu, tab = tab type mega menu, category = category type mega menu');
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('parent')->nullable()->index('menu_item_parent_foreign');
            $table->string('source', 191);
            $table->string('url', 191)->nullable();
            $table->unsignedBigInteger('page_id')->nullable()->index('menu_item_page_id_foreign');
            $table->unsignedBigInteger('category_id')->nullable()->index('menu_item_category_id_foreign');
            $table->unsignedBigInteger('sub_category_id')->nullable()->index('menu_item_sub_category_id_foreign');
            $table->unsignedBigInteger('post_id')->nullable()->index('menu_item_post_id_foreign');
            $table->boolean('status')->default(false);
            $table->boolean('new_teb')->default(false);
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
        Schema::dropIfExists('menu_item');
    }
}
