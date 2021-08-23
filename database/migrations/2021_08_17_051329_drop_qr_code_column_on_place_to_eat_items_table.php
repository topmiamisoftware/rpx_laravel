<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropQrCodeColumnOnPlaceToEatItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_to_eat_items', function (Blueprint $table) {                   
            $table->dropColumn('qr_code_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('place_to_eat_items', function (Blueprint $table) {
            //
        });
    }
}
