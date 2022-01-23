<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotbieAds extends Model
{
    use HasFactory;

    static function getSpotbieAd($adType){    

        $adId = 0;

        switch($adType){
            case 0:
                $adId = rand(1, 2);
                break;
            case 1:
                $adId = rand(3, 6);
                break;
            case 2:
                $adId = rand(1, 2);
                break;
        }

        return SpotbieAds::select('*')
        ->where('id', $adId)
        ->first();
    }
}
