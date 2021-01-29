<?php

namespace App\Models;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

use App\Models\User;

class Stream extends Model
{
    use SoftDeletes;
    /* 
    * Get the user who initiated the relationship.
    */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'user_id', 'user_id');
    }

    public function myStream(Request $request){

        if(!Auth::check()){

            $validatedData = $request->validate([
                'user_id' => 'required|integer'
            ]);

            $user = User::where('id', $validatedData['user_id'])
            ->first();
            
        } else {
            $user = Auth::user();
        }

        $stream_post_list = $user->streamPosts()
        ->select('id', 'original_post_id', 'user_id', 'stream_content', 'extra_media', 'created_at', 'updated_at', 'status')
        ->where('status', '1')
        ->withCount('comments')
        ->withCount('likes')
        ->with('extraMediaList:id,type,content,stream_post_id,owner_user_id,status,created_at,updated_at')
        ->with('user:id,username')
        ->with('spotbieUser:id,default_picture')
        ->with('webOptions:time_zone')
        ->orderBy('updated_at', 'desc')      
        ->paginate(10);

        $response = array(
            'message' => 'success',
            'stream_post_list' => $stream_post_list
        );

        return response($response);

    }

    public function myGeneralStream(Request $request){

        $user = Auth::user();

        if(!Auth::check()){

            $response = array(
                'message' => 'failed'
            );
    
            return response($response);

        }

        $stream_post_list = StreamPost::
        select('id', 'original_post_id', 'user_id', 'stream_content', 'extra_media', 'created_at', 'updated_at', 'status')
        ->where('status', 1)
        ->whereIn('user_id', 
            $user->relationships()
            ->where('relation', 1) 
            ->pluck('peer_id')
        )
        ->withCount('comments')
        ->withCount('likes')
        ->with('extraMediaList:id,type,content,stream_post_id,owner_user_id,status,created_at,updated_at')
        ->with('user:id,username')
        ->with('spotbieUser:id,default_picture')
        ->with('webOptions:time_zone')
        ->orderBy('updated_at', 'desc')      
        ->paginate(10);

        $response = array(
            'message' => 'success',
            'stream_post_list' => $stream_post_list
        );

        return response($response);

    }

}
