<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 191)->unique();
            $table->string('phone', 191);
            $table->date('dob')->comment('date of birth');
            $table->boolean('gender')->comment('1 male, 2 female, 3 others');
            $table->string('password', 191)->nullable();
            $table->longText('permissions')->nullable()->comment('it will be array data');
            $table->timestamp('last_login')->nullable();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('profile_image', 191)->nullable();
            $table->unsignedBigInteger('image_id')->nullable()->index('users_image_id_foreign');
            $table->boolean('newsletter_enable')->default(true);
            $table->boolean('is_user_banned')->default(true)->comment('0 banned, 1 unbanned');
            $table->boolean('is_password_set')->default(false)->comment('0 not set, 1 set');
            $table->text('user_banned_reason')->nullable();
            $table->boolean('is_subscribe_banned')->default(true)->comment('0 banned, 1 unbanned');
            $table->text('subscribe_banned_reason')->nullable();
            $table->text('about_us')->nullable();
            $table->longText('social_media')->comment('it will be array data');
            $table->boolean('is_active')->default(true)->comment('0 inactive user, 1 active user');
            $table->text('deactivate_reason')->nullable();
            $table->unsignedInteger('firebase_auth_id')->nullable()->comment('this is for mobile app.');
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
        Schema::dropIfExists('users');
    }
}
