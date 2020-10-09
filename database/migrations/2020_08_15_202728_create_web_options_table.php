<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('web_options', function (Blueprint $table) {

            $table->id();
            $table->unsignedInteger('user_id')->references('id')->on('users')->unique();
            $table->string('spotmee_bg')->nullable();
            $table->timestamp('spotmee_bg_date')->nullable();
            $table->string('bg_color')->default('#101010');
            $table->string('time_zone')->default('America/New_York');
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
        Schema::dropIfExists('web_options');
    }
}
