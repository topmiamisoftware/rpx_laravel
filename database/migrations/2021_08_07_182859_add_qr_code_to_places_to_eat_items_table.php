<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrCodeToPlacesToEatItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_to_eat_items', function (Blueprint $table) {                   
            $table->string('qr_code_link', 135)->nullable(false);
            $table->float('qr_coin_value')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('places_to_eat_items', function (Blueprint $table) {
            $table->string('qr_code_link', 135)->nullable(false);
            $table->float('qr_coin_value')->nullable(false)->default(0);
        });
    }
}
