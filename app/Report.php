<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    protected $fillable = ['user_id', 'peer_id', 'report_reason', 'created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
