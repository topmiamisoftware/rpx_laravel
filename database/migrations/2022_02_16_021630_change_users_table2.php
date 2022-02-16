<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsersTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city', 255)->nullable()->default(config('spotbie.my_city'));
            $table->string('country', 255)->nullable()->default(config('spotbie.my_country'));
            $table->string('line1', 255)->nullable()->default(config('spotbie.my_line_1'));
            $table->string('postal_code', 255)->nullable()->default(config('spotbie.my_zip_code'));
            $table->string('state', 255)->nullable()->default(config('spotbie.my_state'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 235)->unique()->change();
        });
    }
}
