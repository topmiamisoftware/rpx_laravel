<?php

namespace App\Models;

use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{

    use HasFactory, SoftDeletes; 

    public function business(){
        return $this->belongsTo('App\Models\Business', 'business_id', 'id');
    }

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
    
    public function index(){

        $user = Auth::user();

        if(!$user){

            $response = array(
                "success" => false,
                "message" => "You are not authorized to view this content." 
            );
    
            return response($response); 

        } 

        $adList = $user->business->ads()->get();

        $response = array(
            "success" => true,
            "adList" => $adList 
        );

        return response($response);

    }

}
