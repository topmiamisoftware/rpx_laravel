<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotbieAds extends Model
{
    use HasFactory;

    public static function getSpotbieAd($adType)
    {
        $adId = 0;
        $query = null;

        switch($adType)
        {
            case 0:
            case 2:
                $adId = rand(1, 2);
                $query = SpotbieAds::select('*')
                ->where('id', $adId)
                ->first();
                break;
            case 1:
                $adId = rand(3, 5);
                $query = SpotbieAds::select('*')
                ->where('id', $adId)
                ->first();
                break;
        }

        return $query;
    }
}
