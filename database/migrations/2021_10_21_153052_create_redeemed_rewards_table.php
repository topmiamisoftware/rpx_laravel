<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeemedRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeemed_rewards', function (Blueprint $table) {

            $table->id();
            $table->uuid('uuid')->nullable(false)->default(Str::uuid())->unique();
            $table->unsignedBigInteger('business_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('redeemer_id')->references('id')->on('users')->nullable()->default(null);
            $table->float('amount')->nullable(false)->default(0);
            $table->float('total_spent')->nullable(false)->default(0);
            $table->float('dollar_value')->nullable(false)->default(0);
            $table->float('loyalty_point_dollar_percent_value')->nullable(false)->default(0);
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
        Schema::dropIfExists('redeemed_rewards');
    }
}
