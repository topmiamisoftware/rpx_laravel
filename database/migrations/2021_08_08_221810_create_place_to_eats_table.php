<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaceToEatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places_to_eat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->string('name', 100)->nullable(false);
            $table->string('description', 500)->nullable(false);
            $table->string('address', 100)->nullable(false);
            $table->float('loc_x', 8, 6)->nullable();
            $table->float('loc_y', 8, 6)->nullable();                        
            $table->string('photo', 650)->nullable(false)->default('');  
            $table->boolean('is_verified')->nullable(false)->default(false);
            $table->string('qr_code_link', 135)->nullable(false);      
            $table->timestamps();
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
    }
}
