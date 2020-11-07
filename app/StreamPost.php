<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StreamPost extends Model
{
    use SoftDeletes;
    /* 
    * Get the user who initiated the relationship.
    */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function spotbieUser(){
        return $this->belongsTo('App\SpotbieUser', 'user_id', 'id');
    }

    public function webOptions(){
        return $this->hasOneThrough('App\WebOptions', 'App\User', 'id', 'user_id', 'user_id');
    }

    /* 
    * Get the user's stream.
    */
    public function stream(){
        return $this->belongsTo('App\Stream', 'stream_id');
    } 

    /* 
    * Get the user who initiated the relationship.
    */
    public function originalPost(){
        return $this->belongsTo('App\StreamPost', 'original_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function likes(){
        return $this->hasMany('App\StreamPostLike', 'stream_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function comments(){
        return $this->hasMany('App\StreamPostComment', 'stream_post_id');
    }
    
    public function extraMediaList(){
        return $this->hasMany('App\ExtraMedia', 'stream_post_id');
    }

    public function friendships(){
        return $this->belongsToMany('App\Friendship', 'friendships');
    }

}
