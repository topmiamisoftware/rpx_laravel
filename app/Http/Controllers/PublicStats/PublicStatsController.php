<?php

namespace App\Http\Controllers\PublicStats;

use App\Http\Controllers\Controller;

use App\Services\PublicStats;

use Illuminate\Http\Request;

class PublicStatsController extends Controller
{
    
    public function getTotalUsers(PublicStats $publicStats){
        return $publicStats->getTotalUsers();
    } 

}
