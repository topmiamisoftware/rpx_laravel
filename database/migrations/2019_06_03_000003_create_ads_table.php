<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('subscription_id')->references('id')->on('subscriptions')->nullable();
            $table->uuid('uuid')->nullable(false)->default(Str::uuid())->unique();
            $table->unsignedBigInteger('business_id')->references('id')->on('business');
            $table->smallInteger('type');
            $table->string('name', 50);
            $table->string('description', 150);   
            $table->string('images', 500);
            $table->float('dollar_cost')->nullable(false);  
            $table->integer('clicks')->nullable(false)->default(0);
            $table->integer('views')->nullable(false)->default(0);
            $table->boolean('is_live')->nullable(false)->default(0);  
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
        Schema::dropIfExists('ads');
    }
}
