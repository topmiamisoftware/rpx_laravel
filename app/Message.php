<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /* 
    * Get the user who initiated the relationship.
    */
    public function sendingUser(){
        return $this->belongsTo('App\User', 'by');
    }  
    
    /* 
    * Get the user who initiated the relationship.
    */
    public function receivingUser(){
        return $this->belongsTo('App\User', 'to');
    }       

    /* 
    * Get the user who initiated the relationship.
    */
    public function notification(){
        return $this->hasOne('App\Message', 'message_id');
    }

}
