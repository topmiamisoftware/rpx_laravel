<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{

    use SoftDeletes;

    /* 
    * Get the user who initiated the relationship.
    */
    public function sendingUser(){
        return $this->belongsTo('App\Models\User', 'by');
    }  
    
    /* 
    * Get the user who initiated the relationship.
    */
    public function receivingUser(){
        return $this->belongsTo('App\Models\User', 'to');
    }       

    /* 
    * Get the user who initiated the relationship.
    */
    public function notification(){
        return $this->hasOne('App\Models\Message', 'message_id');
    }

}
