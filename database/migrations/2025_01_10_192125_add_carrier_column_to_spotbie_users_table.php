<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarrierColumnToSpotbieUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spotbie_users', function (Blueprint $table) {
            $table->string('carrier', 600);
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
            $table->dropColumn('carrier');
        });
    }
}
