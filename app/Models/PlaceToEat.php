<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PlaceToEat extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'places_to_eat';

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    } 

}
