<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlbumItemLike extends Model
{

    use SoftDeletes;

    public function albumItem(){
        return $this->belongsTo('App\AlbumItem', 'album_media_id');
    }

    public function album(){
        return $this->belongsTo('App\Album', 'album_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }     

    public function likeAlbumItem(){
        
    }

}
