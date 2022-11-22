<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('video_name', 191);
            $table->text('video_thumbnail');
            $table->string('disk', 191);
            $table->text('original');
            $table->text('v_144p')->nullable();
            $table->text('v_240p')->nullable();
            $table->text('v_360p')->nullable();
            $table->text('v_480p')->nullable();
            $table->text('v_720p')->nullable();
            $table->text('v_1080p')->nullable();
            $table->string('video_type', 191)->nullable();
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
        Schema::dropIfExists('videos');
    }
}
