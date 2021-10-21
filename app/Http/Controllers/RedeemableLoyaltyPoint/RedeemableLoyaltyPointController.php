<?php

namespace App\Http\Controllers\RedeemableLoyaltyPoint;

use App\Http\Controllers\Controller;
use App\Models\RedeemableLoyaltyPoint;
use Illuminate\Http\Request;

class RedeemableLoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {
        return $redeemableLoyaltyPoint->index($request);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {   
        return $redeemableLoyaltyPoint->create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RedeemableLoyaltyPoint  $redeemableLoyaltyPoint
     * @return \Illuminate\Http\Response
     */
    public function show(RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RedeemableLoyaltyPoint  $redeemableLoyaltyPoint
     * @return \Illuminate\Http\Response
     */
    public function edit(RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RedeemableLoyaltyPoint  $redeemableLoyaltyPoint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RedeemableLoyaltyPoint  $redeemableLoyaltyPoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(RedeemableLoyaltyPoint $redeemableLoyaltyPoint)
    {
        //
    }

    public function redeem(Request $request, RedeemableLoyaltyPoint $redeemableLoyaltyPoint){
        return $redeemableLoyaltyPoint->redeem($request);
    }
    
}
