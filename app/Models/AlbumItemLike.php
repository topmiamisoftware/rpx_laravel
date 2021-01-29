<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlbumItemLike extends Model
{

    use SoftDeletes;

    public function albumItem(){
        return $this->belongsTo('App\Models\AlbumItem', 'album_media_id');
    }

    public function album(){
        return $this->belongsTo('App\Album', 'album_id');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }     

    public function likeAlbumItem(){
        
    }

}
