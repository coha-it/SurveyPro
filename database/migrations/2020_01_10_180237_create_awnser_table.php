<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwnserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awnsers', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id');
            $table->bigInteger('question_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('awnsers', function (Blueprint $table) {
            // Connect Foreign Keys
            $table->foreign('question_id')->references('id')->on('questions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awnsers');
    }
}
