<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('language_id');
            $table->string('name', 191);
            $table->string('script', 191)->nullable();
            $table->string('native', 191)->nullable();
            $table->string('regional', 191)->nullable();
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
        Schema::dropIfExists('language_configs');
    }
}
