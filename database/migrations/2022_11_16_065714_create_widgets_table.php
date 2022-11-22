<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->string('language', 191)->default('en');
            $table->longText('content')->nullable();
            $table->string('short_code', 191)->nullable()->unique();
            $table->unsignedInteger('order')->default(1);
            $table->integer('location')->default(1);
            $table->integer('content_type')->default(1);
            $table->boolean('status')->default(true);
            $table->boolean('is_custom')->default(true);
            $table->unsignedBigInteger('ad_id')->nullable()->index('widgets_ad_id_foreign');
            // $table->unsignedBigInteger('poll_id')->nullable()->index('widgets_poll_id_foreign');
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
        Schema::dropIfExists('widgets');
    }
}
