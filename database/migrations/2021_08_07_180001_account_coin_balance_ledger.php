<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountCoinBalanceLedger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_coin_balances_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->references('user_id')->on('account_coin_balances');
            $table->float('coin_cost')->nullable(false)->default(0); 
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
        //
    }
}
