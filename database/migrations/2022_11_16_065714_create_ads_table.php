<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad_name', 191)->nullable();
            $table->string('ad_size', 191)->nullable();
            $table->string('ad_type', 191)->nullable();
            $table->string('ad_url', 191)->nullable();
            $table->unsignedBigInteger('ad_image_id')->nullable()->index('ads_ad_image_id_foreign');
            $table->longText('ad_code')->nullable();
            $table->longText('ad_text')->nullable();
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
        Schema::dropIfExists('ads');
    }
}
