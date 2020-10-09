<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpotbieUser extends Model
{
    use HasFactory;
    /* 
    * SpotBie User belogns to User.
    */
    public function user(){
        return $this->belongsTo('App\User', 'id');
    }

    public function userLocation(){
        return $this->hasOne('App\UserLocation', 'user_id');
    }
    
    public function streamPost(){
        return $this->hasMany('App\StreamPost', 'user_id');
    }

    public function store(){
        
    }

}
