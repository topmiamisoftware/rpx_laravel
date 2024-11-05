<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoterDeviceAlternatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoter_device_alternators', function (Blueprint $table) {
            $table->id();
            $table->float('loc_x', 8, 6)->nullable();
            $table->float('loc_y', 8, 6)->nullable();
            $table->string('business_list', 2500)->nullable();
            $table->string('device_id', 65)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promoter_device_alternators');
    }
}
