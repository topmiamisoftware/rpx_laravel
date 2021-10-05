<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{

    use HasFactory, SoftDeletes; 

    public function getSingleAdBanner(){

        $ad = $this
        ->where('type', 1)
        ->inRandomOrder()
        ->limit(1)
        ->get()
        ->first();

        $response = array(
            "success" => true,
            "ad" => $ad 
        );

        return response($response);

    }    
}
