<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign(['video_thumbnail_id'])->references(['id'])->on('images')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign(['sub_category_id'])->references(['id'])->on('sub_categories')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign(['video_id'])->references(['id'])->on('videos')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign(['image_id'])->references(['id'])->on('images')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign('posts_user_id_foreign');
            $table->dropForeign('posts_video_thumbnail_id_foreign');
            $table->dropForeign('posts_category_id_foreign');
            $table->dropForeign('posts_sub_category_id_foreign');
            $table->dropForeign('posts_video_id_foreign');
            $table->dropForeign('posts_image_id_foreign');
        });
    }
}
