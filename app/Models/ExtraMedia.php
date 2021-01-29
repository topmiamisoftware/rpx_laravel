<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraMedia extends Model
{
    use SoftDeletes;
    /* 
    * Get the stream post this media belongs to.
    */
    public function streamPost(){
        return $this->belongsTo('App\Models\StreamPost', 'stream_post_id');
    } 

    /* 
    * Get the user this media belongs to.
    */
    public function user(){
        return $this->belongsTo('App\Models\User', 'owner_user_id');
    } 
    
    /* 
    * Get the stream this media belongs to.
    */
    public function stream(){
        return $this->belongsTo('App\Models\Stream', 'stream_id');
    }        

}
