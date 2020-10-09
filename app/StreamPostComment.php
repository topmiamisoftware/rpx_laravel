<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreamPostComment extends Model
{
    /* 
    * Get the user who initiated the relationship.
    */
    public function streamPost(){
        return $this->belongsTo('App\StreamPost', 'stream_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function notification(){
        return $this->hasOne('App\StreamPostCommentNotification', 'stream_post_comment_id');
    }    

}
