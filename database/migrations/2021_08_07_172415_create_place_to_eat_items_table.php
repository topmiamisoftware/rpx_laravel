<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaceToEatItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_to_eat_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('place_to_eat_id')->references('id')->on('places_to_eat');
            $table->smallInteger('type');
            $table->string('name', 50);
            $table->string('description', 150);   
            $table->string('images', 500);
            $table->float('point_cost')->nullable(false);  
            $table->integer('monthly_times_available')->nullable(false)->default(0);
            $table->integer('times_claimed_this_month')->nullable(false)->default(0);
            $table->timestamps();
            $table->timestamp('ends_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('place_to_eat_items');
    }
}
