<?php

namespace App\Http\Controllers\WebOptions;

use App\Http\Controllers\Controller;

use App\Models\WebOptions;

use Illuminate\Http\Request;

class WebOptionsController extends Controller
{
    
    public function show(WebOptions $webOptions)
    {
        //will return user info for display
        return $webOptions->getWebOptions();
    }
    

    public function setBgColor(WebOptions $webOptions, Request $request)
    {
        //will return user info for display
        return $webOptions->setBgColor($request);
    }


}   
