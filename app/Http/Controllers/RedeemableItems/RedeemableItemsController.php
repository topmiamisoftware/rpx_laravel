<?php

namespace App\Http\Controllers\RedeemableItems;

use App\Http\Controllers\Controller;
use App\Models\RedeemableItems;
use Illuminate\Http\Request;

class RedeemableItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RedeemableItems $redeemableItems)
    {
        return $redeemableItems->index($request);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, RedeemableItems $redeemableItems)
    {   
        return $redeemableItems->create($request);
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
     * @param  \App\Models\RedeemableItems  $redeemableItems
     * @return \Illuminate\Http\Response
     */
    public function show(RedeemableItems $redeemableItems)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RedeemableItems  $redeemableItems
     * @return \Illuminate\Http\Response
     */
    public function edit(RedeemableItems $redeemableItems)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RedeemableItems  $redeemableItems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RedeemableItems $redeemableItems)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RedeemableItems  $redeemableItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(RedeemableItems $redeemableItems)
    {
        //
    }

    public function redeem(Request $request, RedeemableItems $redeemableItems){
        return $redeemableItems->redeem($request);
    }
    
}
