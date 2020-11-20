<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Ads;

class AdsController extends Controller
{
    public function getSingleAdBanner(Ads $ads){
        return $ads->getSingleAdBanner();
    }
}
