<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlbumItemComment extends Model
{

    use SoftDeletes;

    public function albumItem(){
        return $this->belongsTo('App\Models\AlbumItem', 'album_item_id');
    }

    public function album(){
        return $this->belongsTo('App\Album', 'album_id');
    } 

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }  

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'user_id');
    }

    public function addAlbumMediaComment(){
        
    }

}
