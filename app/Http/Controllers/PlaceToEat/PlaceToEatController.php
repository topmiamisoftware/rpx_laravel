<?php

namespace App\Http\Controllers\PlaceToEat;

use App\Http\Controllers\Controller;

use App\Models\PlaceToEat;
use Illuminate\Http\Request;

class PlaceToEatController extends Controller
{

    public function verify(Request $request, PlaceToEat $placeToEat)
    {
        return $placeToEat->verify($request);
    }

    public function getGooglePlacesToEat(Request $request, PlaceToEat $placeToEat)
    {
        return $placeToEat->getGooglePlacesToEat($request);
    }

}
