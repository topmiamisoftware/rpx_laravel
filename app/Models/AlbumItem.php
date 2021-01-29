<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlbumItem extends Model
{

    use SoftDeletes;

    public function comments(){
        return $this->hasMany('App\Models\AlbumItemComment', 'album_item_id');
    }

    public function likes(){
        return $this->hasMany('App\Models\AlbumItemLike', 'album_media_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'media_owner_user_id');
    }

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'media_owner_user_id');
    }

    public function album(){
        return $this->belongsTo('App\Album', 'album_id');
    }

    public function showComments(Request $request){

        $album = $this->select('album_id')
        ->where('id', $this->id)
        ->first()
        ->album()
        ->select('id')
        ->first();

        //Get the client's viewing rights to the album.
        $view_right = Gate::inspect('viewAlbum', $album);

        if($view_right->allowed()){
            
            $comments = $this->comments()
            ->with('user:id,username')
            ->with('spotbieUser:id,default_picture')
            ->paginate(10);

            if(count($comments) > 0){
                $message = 'success';
                $comment_list = $comments;
            } else {
                $message = 'no_comments';
                $comment_list = null;
            }
    
        } else {
            
            $message = $view_right->message();
            $comment_list = null;

        }

        $response = array(
            "message" => $message,
            "comment_list" => $comment_list
        );

        return $response;

    }

    public function deleteAllUnused(){
        
    }

}
