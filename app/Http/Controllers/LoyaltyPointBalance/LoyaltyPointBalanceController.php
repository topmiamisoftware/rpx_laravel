<?php

namespace App\Http\Controllers\LoyaltyPointBalance;

use App\Http\Controllers\Controller;

use App\Models\LoyaltyPointBalance;
use Illuminate\Http\Request;

class LoyaltyPointBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoyaltyPointBalance $loyaltyPointBalance, Request $request)
    {
        return $loyaltyPointBalance->store($request);
    }

    /**
     * Increment the LoyaltyPoint account balance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(LoyaltyPointBalance $loyaltyPointBalance, Request $request)
    {
        return $loyaltyPointBalance->add($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoyaltyPointBalance  $LoyaltyPointBalance
     * @return \Illuminate\Http\Response
     */
    public function show(LoyaltyPointBalance $loyaltyPointBalance)
    {
        return $loyaltyPointBalance->show();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoyaltyPointBalance  $LoyaltyPointBalance
     * @return \Illuminate\Http\Response
     */
    public function edit(LoyaltyPointBalance $LoyaltyPointBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoyaltyPointBalance  $LoyaltyPointBalance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoyaltyPointBalance $LoyaltyPointBalance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoyaltyPointBalance  $LoyaltyPointBalance
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoyaltyPointBalance $LoyaltyPointBalance)
    {
        //
    }

}
