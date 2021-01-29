<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StreamPostComment extends Model
{
    use SoftDeletes;
    /* 
    * Get the user who initiated the relationship.
    */
    public function streamPost(){
        return $this->belongsTo('App\Models\StreamPost', 'stream_post_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /* 
    * Get the user who initiated the relationship.
    */
    public function notification(){
        return $this->hasOne('App\Models\StreamPostCommentNotification', 'stream_post_comment_id');
    }    

}
