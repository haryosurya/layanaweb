<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRssFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rss_feeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('feed_url', 191);
            $table->string('language', 191);
            $table->unsignedBigInteger('category_id')->nullable()->index('rss_feeds_category_id_foreign');
            $table->unsignedBigInteger('sub_category_id')->nullable()->index('rss_feeds_sub_category_id_foreign');
            $table->smallInteger('post_limit');
            $table->boolean('auto_update')->default(false);
            $table->boolean('show_read_more')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('keep_date')->default(false);
            $table->string('meta_keywords', 191)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('tags', 191)->nullable();
            $table->timestamp('scheduled_date')->nullable();
            $table->string('layout', 191)->default('default');
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
        Schema::dropIfExists('rss_feeds');
    }
}
