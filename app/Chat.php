<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    //
    /* 
    * Get the user who initiated the relationship.
    */
    public function messages(){
        return $this->hasMany('App\Message', 'message_id');
    }
      
}
