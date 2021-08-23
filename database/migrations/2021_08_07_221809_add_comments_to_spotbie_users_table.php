<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentsToSpotbieUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spotbie_users', function (Blueprint $table) {
            $table->integer('user_type', false, true)
            ->default('0')
            ->change()
            ->comments = "0 unset_user; 99 admin; 1 place to eat; 2 events; 3 retail shop; 4 regular user;";
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
            //
        });
    }
}
