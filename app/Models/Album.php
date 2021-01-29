<?php

namespace App\Models;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{

    use SoftDeletes;

    public function albumItems(){
        return $this->hasMany('App\Models\AlbumItem');
    }

    public function comments(){
        return $this->hasMany('App\Models\AlbumItemComment');
    } 
    
    public function likes(){
        return $this->hasMany('App\Models\AlbumItemLike');
    }     

    public function user(){
        return $this->belongsTo('App\Models\User', 'owner_user_id');
    }  

    public function update(Request $request){

    }

    public function myAlbums(){

        $user = Auth::user();

        $album_list = $user
        ->albums()
        ->withCount('comments')
        ->withCount('likes')        
        ->orderByDesc('created_at')
        ->paginate(10);

        $message = "success";

        $response = array(
            "message" => $message,
            "album_list" => $album_list  
        );

        return response($response);

    }

    public function publicAlbums(Request $request){
        
        $validatedData = $request->validate([
            'peer_id' => 'required|int'
        ]);
        
        $peer_id = $validatedData['peer_id'];

        if(Auth::check()){

            $user = Auth::user();

            $usersAreFriends = $user
            ->relationships()
            ->select('user_id', 'peer_id')
            ->where('user_id', $user->id)
            ->where('peer_id', $peer_id)
            ->where('relation', 1)
            ->first();
            
            if($usersAreFriends !== null || $peer_id == $user->id){

                //Users are friends or User is viewing their own profile.

                $album_list = $this
                ->where('user_id', $peer_id)
                ->withCount('comments')
                ->withCount('likes')        
                ->orderByDesc('created_at')
                ->paginate(10);

            } else {

                $album_list = $this
                ->where('user_id', $peer_id)
                ->where('privacy', 0)
                ->withCount('comments')
                ->withCount('likes')        
                ->orderByDesc('created_at')
                ->paginate(10);

            }
        
            $message = "success";

        } else {

            //Get non-private albums since user is not logged in

            $album_list = $this
            ->where('user_id', $peer_id)
            ->where('privacy', '0')
            ->withCount('comments')
            ->withCount('likes')
            ->orderByDesc('created_at')
            ->paginate(10);

            $message = "success";

        }

        $response = array(
            "message" => $message,
            "album_list" => $album_list  
        );

        return response($response);

    }

    public function viewAlbum(Request $request){

        //Get the client's viewing rights to the album.
        $view_right = Gate::inspect('viewAlbum', $this);

        if( $view_right->allowed() ){
            
            $message = 'success';

            $album_item_list = $this
            ->albumItems()
            ->select('id', 'album_owner_user_id', 'media_owner_user_id', 'album_id', 
            'loc_x', 'loc_y', 'media_type', 'caption', 'content', 'created_at', 'updated_at')
            ->withCount('likes')
            ->withCount('comments')
            ->orderBy('id', 'desc')
            ->paginate(10);

            $album_settings = $this
            ->select('id', 'name', 'description', 'privacy', 'cover', 'created_at')
            ->where('id', $this->id)
            ->first();

        } else {

            $message = $view_right->message();

            $album_item_list = null;
            $album_settings = null;
            
        }        

        $response = array(
            "message" => $message,
            "album_item_list" => $album_item_list,
            "album_settings" => $album_settings
        );

        return response($response);

    }

    public function slideShowSet(Request $request){

        $validatedData = $request->validate([
            'item_id' => 'required|int'
        ]);

        //Get the client's viewing rights to the album.
        $view_right = Gate::inspect('viewAlbum', $this);
        
        if($view_right->allowed()){

            $message = 'success';
            
            $previous = $this
            ->albumItems()
            ->where('id', '>', $validatedData['item_id'])
            ->orderBy('id', 'asc')
            ->limit(1)
            ->get();

            if(count($previous) > 0){

                $previous = $previous[0];

            } else {

                $previous = $this
                ->albumItems()
                ->orderBy('id', 'asc')
                ->limit(1)
                ->get()[0];
            
            }

            $next = $this
            ->albumItems()
            ->where('id', '<', $validatedData['item_id'])
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();

            if(count($next) > 0){

                $next = $next[0];

            } else {

                $next = $this
                ->albumItems()
                ->orderBy('id', 'desc')
                ->limit(1)
                ->get()[0];
            
            }

        } else {

            $message = $view_right->message();
            $next = null;
            $previous = null;

        }

        $response = array(
            "message" => $message,
            "next" => $next,
            "previous" => $previous
        );

        return response($response);

    }

}