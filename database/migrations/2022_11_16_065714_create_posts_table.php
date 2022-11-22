<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->string('slug', 191)->unique();
            $table->text('content');
            $table->string('language', 191);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index('posts_category_id_foreign');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->enum('post_type', ['article', 'video', 'audio', 'trivia-quiz', 'personality-quiz'])->nullable();
            $table->boolean('submitted')->default(false)->comment('0 Non Submitted, 1 submitted');
            $table->unsignedBigInteger('image_id')->nullable()->index('posts_image_id_foreign');
            $table->boolean('visibility')->default(false);
            $table->boolean('auth_required')->default(false);
            $table->boolean('slider')->default(false);
            $table->integer('slider_order')->default(0);
            $table->boolean('featured')->default(false);
            $table->integer('featured_order')->default(0);
            $table->boolean('breaking')->default(false);
            $table->integer('breaking_order')->default(0);
            $table->boolean('recommended')->default(false);
            $table->integer('recommended_order')->default(0);
            $table->boolean('editor_picks')->default(false);
            $table->integer('editor_picks_order')->default(0);
            $table->boolean('scheduled')->default(false);
            $table->text('meta_title')->nullable();
            $table->string('meta_keywords', 191)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('tags', 191)->nullable();
            $table->timestamp('scheduled_date')->nullable();
            $table->string('layout', 191)->nullable()->default('default');
            $table->unsignedBigInteger('video_id')->nullable()->index('posts_video_id_foreign');
            $table->string('video_url_type', 191)->nullable();
            $table->text('video_url')->nullable();
            $table->unsignedBigInteger('video_thumbnail_id')->nullable()->index('posts_video_thumbnail_id_foreign');
            $table->boolean('status')->default(false);
            $table->bigInteger('total_hit')->default(0);
            $table->longText('contents')->nullable()->comment('extra content');
            $table->text('read_more_link')->nullable()->comment('rss post actual link');
            $table->timestamps();

            $table->index(['recommended_order', 'featured_order', 'id']);
            $table->index(['visibility', 'status', 'slider', 'language', 'auth_required']);
            $table->index(['created_at', 'updated_at']);
            $table->index(['user_id', 'category_id']);
            $table->index(['sub_category_id', 'video_thumbnail_id']);
            $table->index(['post_type', 'video_url_type', 'total_hit']);
            $table->index(['featured', 'breaking', 'recommended', 'editor_picks', 'tags']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
