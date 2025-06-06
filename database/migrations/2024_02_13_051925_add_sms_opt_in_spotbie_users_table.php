<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSmsOptInSpotbieUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spotbie_users', function (Blueprint $table) {
            $table->boolean('sms_opt_in')->default(false);
            $table->boolean('phone_confirmed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spotbie_users', function (Blueprint $table) {
            $table->dropColumn('sms_opt_in');
            $table->dropColumn('phone_confirmed');
        });
    }
}
