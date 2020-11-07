<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;
    //
    /* 
    * Get the user who initiated the relationship.
    */
    public function messages(){
        return $this->hasMany('App\Message', 'message_id');
    }
      
}
