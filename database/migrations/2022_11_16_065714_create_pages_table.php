<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('image_id')->nullable()->index('pages_image_id_foreign');
            $table->string('title', 191);
            $table->string('language', 191);
            $table->boolean('page_type')->default(true)->comment('1 default page, 2 contact us page');
            $table->string('slug', 191)->unique();
            $table->longText('description')->nullable();
            $table->string('template', 191)->default('1')->comment('1 without sidebar, 2 with right sidebar, 3 with left sidebar');
            $table->string('visibility', 191);
            $table->boolean('show_for_register');
            $table->boolean('show_title');
            $table->boolean('show_breadcrumb');
            $table->string('location', 191)->nullable();
            $table->string('meta_title', 191)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 191)->nullable();
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
        Schema::dropIfExists('pages');
    }
}
