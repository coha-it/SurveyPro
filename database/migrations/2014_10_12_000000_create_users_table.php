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
            $table->string('pan', 8)->nullable()->unique();
            $table->string('pin', 8)->nullable();

            // Login Fails
            $table->tinyInteger('failed_logins')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Timestamps
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
