<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('disk', 191);
            $table->string('original_image', 191);
            $table->string('og_image', 191);
            $table->string('thumbnail', 191);
            $table->string('big_image', 191);
            $table->string('big_image_two', 191);
            $table->string('medium_image', 191);
            $table->string('medium_image_two', 191);
            $table->string('medium_image_three', 191);
            $table->string('small_image', 191);
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
        Schema::dropIfExists('images');
    }
}
