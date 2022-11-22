<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemeSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('theme_id');
            $table->tinyInteger('type')->default(0);
            $table->string('label', 191);
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('category_id')->nullable()->index('theme_sections_category_id_foreign');
            $table->unsignedBigInteger('ad_id')->nullable()->index('theme_sections_ad_id_foreign');
            $table->unsignedBigInteger('post_amount')->nullable();
            $table->string('section_style', 191)->nullable();
            $table->tinyInteger('is_primary')->default(0);
            $table->boolean('status')->default(false);
            $table->string('language', 191)->nullable();
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
        Schema::dropIfExists('theme_sections');
    }
}
