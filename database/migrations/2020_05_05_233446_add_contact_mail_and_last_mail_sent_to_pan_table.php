<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactMailAndLastMailSentToPanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('u_pans', function (Blueprint $table) {
            $table->string('contact_mail')->nullable()->after('locked_until');
            $table->dateTime('last_mail_sent')->nullable()->after('contact_mail');
            $table->dateTime('last_mail_status')->nullable()->after('last_mail_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('u_pans', function (Blueprint $table) {
            $table->dropColumn('contact_mail');
            $table->dropColumn('last_mail_sent');
            $table->dropColumn('last_mail_status');
        });
    }
}
