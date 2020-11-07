<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpotbieUser extends Model
{
    use HasFactory, SoftDeletes;
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
