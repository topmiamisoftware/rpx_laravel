<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_favorites', function (Blueprint $table) {
            $table->id();
            $table->string('yelp_id', 5)->nullable();
            $table->float('loc_x', 8, 6)->nullable();
            $table->float('loc_y', 8, 6)->nullable();            
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
        Schema::dropIfExists('my_favorites');
    }
}
