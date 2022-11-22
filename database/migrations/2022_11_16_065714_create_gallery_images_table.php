<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('disk', 191);
            $table->unsignedBigInteger('album_id')->nullable()->index('gallery_images_album_id_foreign');
            $table->string('tab', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->boolean('is_cover')->default(false)->comment('0 no, 1 yes');
            $table->string('original_image', 191);
            $table->string('thumbnail', 191);
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
        Schema::dropIfExists('gallery_images');
    }
}
