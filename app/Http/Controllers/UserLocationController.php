<?php

namespace App\Http\Controllers;

use App\UserLocation;
use Illuminate\Http\Request;

class UserLocationController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\UserLocation  $userLocation
     * @return \Illuminate\Http\Response
     */
    public function show(UserLocation $userLocation)
    {
        return $userLocation->getMyLocation();
    }

    public function saveCurrentLocation(Request $request, UserLocation $userLocation){
        return $userLocation->saveCurrentLocation($request);
    }   

    public function retrieveSurroundings(UserLocation $userLocation, Request $request)
    {
        //
        return $userLocation->retrieveSurroundings($request);
    }

}
