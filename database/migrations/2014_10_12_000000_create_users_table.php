<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            // ID
            $table->bigIncrements('id');

            // E-Mail Login
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            // PAN & PIN
            $table->boolean('is_pan_user')->default(0);
            $table->string('pan', 8)->nullable()->unique();
            $table->string('pin', 8)->nullable();

            // Login Fails
            $table->tinyInteger('failed_logins')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Token
            $table->rememberToken();

            // Timestamps
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            // Connect Foreign Key
            $table->foreign('created_by')->references('id')->on('users');
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
