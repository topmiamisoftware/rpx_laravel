<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraMedia extends Model
{
    use SoftDeletes;
    /* 
    * Get the stream post this media belongs to.
    */
    public function streamPost(){
        return $this->belongsTo('App\StreamPost', 'stream_post_id');
    } 

    /* 
    * Get the user this media belongs to.
    */
    public function user(){
        return $this->belongsTo('App\User', 'owner_user_id');
    } 
    
    /* 
    * Get the stream this media belongs to.
    */
    public function stream(){
        return $this->belongsTo('App\Stream', 'stream_id');
    }        

}
