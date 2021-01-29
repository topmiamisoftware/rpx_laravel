<?php

namespace App\Http\Controllers\Ads;

use App\Models\Ads;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function getSingleAdBanner(Ads $ads){
        return $ads->getSingleAdBanner();
    }
}
