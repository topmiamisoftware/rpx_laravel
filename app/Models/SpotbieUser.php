<?php

namespace App\Models;

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
        return $this->belongsTo('App\Models\User', 'id');
    }

    public function userLocation(){
        return $this->hasOne('App\Models\UserLocation', 'user_id');
    }
    
    public function streamPost(){
        return $this->hasMany('App\Models\StreamPost', 'user_id');
    }

    public function store(){
        
    }

}
