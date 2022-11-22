<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language', 191);
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->string('tabs', 191)->nullable();
            $table->integer('order')->default(0);
            $table->string('meta_keywords', 191)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('disk', 191);
            $table->string('original_image', 191)->nullable()->comment('cover image');
            $table->string('thumbnail', 191)->nullable()->comment('cover image');
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
        Schema::dropIfExists('albums');
    }
}
