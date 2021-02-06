<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StreamPost extends Model
{
    use SoftDeletes;
    /* 
    * Get the user who initiated the relationship.
    */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'user_id', 'id');
    }

    public function webOptions(){
        return $this->hasOneThrough('App\Models\WebOptions', 'App\Models\User', 'id', 'user_id', 'user_id');
    }

    /* 
    * Get the user's stream.
    */
    public function stream(){
        return $this->belongsTo('App\Models\Stream', 'stream_id');
    } 

    /* 
    * Get the user who initiated the relationship.
    */
    public function originalPost(){
        return $this->belongsTo('App\Models\StreamPost', 'original_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function likes(){
        return $this->hasMany('App\Models\StreamPostLike', 'stream_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function comments(){
        return $this->hasMany('App\Models\StreamPostComment', 'stream_post_id');
    }
    
    public function extraMediaList(){
        return $this->hasMany('App\Models\ExtraMedia', 'stream_post_id');
    }

    public function friendships(){
        return $this->belongsToMany('App\Models\Friendship', 'friendships');
    }

}
