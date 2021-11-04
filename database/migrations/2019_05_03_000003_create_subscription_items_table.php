<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionItemsTable extends Migration
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
            $table->string('stripe_id')->nullable();
            $table->string('stripe_product')->nullable();
            $table->string('stripe_price')->nullable(false);
            $table->integer('quantity')->nullable();
            $table->uuid('uuid')->nullable(false)->default(Str::uuid());
            $table->unsignedBigInteger('business_id')->references('id')->on('business');
            $table->smallInteger('type');
            $table->string('name', 50);
            $table->string('description', 150);   
            $table->string('images', 500);
            $table->float('dollar_cost')->nullable(false);  
            $table->integer('clicks')->nullable(false)->default(0);
            $table->integer('views')->nullable(false)->default(0);
            $table->boolean('is_subscription')->nullable(false)->default(false);
            $table->boolean('failed_subscription')->nullable(false)->default(false);
            $table->boolean('is_live')->nullable(false)->default(false);
            $table->timestamps();
            $table->timestamp('ends_at')->nullable();
            $table->softDeletes();

            $table->unique(['subscription_id', 'stripe_price']);
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
