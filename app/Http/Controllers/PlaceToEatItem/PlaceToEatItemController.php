<?php

namespace App\Http\Controllers\PlaceToEatItem;

use App\Http\Controllers\Controller;

use App\Models\PlaceToEatItem;
use Illuminate\Http\Request;

class PlaceToEatItemController extends Controller
{

    public function create(PlaceToEatItem $placeToEatItem, Request $request)
    {
        return $placeToEatItem->create($request);
    }    

    public function index(PlaceToEatItem $placeToEatItem, Request $request){
        return $placeToEatItem->index($request);
    }
    
    public function update(PlaceToEatItem $placeToEatItem, Request $request)
    {
        return $placeToEatItem->updateReward($request);
    }    

    public function delete(PlaceToEatItem $placeToEatItem, Request $request)
    {
        return $placeToEatItem->deleteMe($request);
    }    

    public function uploadMedia(PlaceToEatItem $placeToEatItem, Request $request){    
        return $placeToEatItem->uploadMedia($request);
    }
}
