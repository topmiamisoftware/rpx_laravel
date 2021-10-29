<?php

namespace App\Http\Controllers\Ads;

use App\Models\Ads;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function headerBanner(Ads $ads, Request $request){
        return $ads->headerBanner($request);
    }

    public function singleAdList(Ads $ads, Request $request){
        return $ads->singleAdList($request);
    }

    public function index(Ads $ads){
        return $ads->index();
    }

    public function create(Ads $ads, Request $request){
        return $ads->create($request);
    }

    public function updateModel(Ads $ads, Request $request){
        return $ads->updateModel($request);
    }

    public function deleteModel(Ads $ads, Request $request){
        return $ads->deleteModel($request);
    }


}
