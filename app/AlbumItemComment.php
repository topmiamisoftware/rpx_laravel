<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlbumItemComment extends Model
{

    public function albumItem(){
        return $this->belongsTo('App\AlbumItem', 'album_item_id');
    }

    public function album(){
        return $this->belongsTo('App\Album', 'album_id');
    } 

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }  

    public function spotbieUser(){
        return $this->belongsTo('App\SpotbieUser', 'user_id');
    }

    public function addAlbumMediaComment(){
        
    }

}
