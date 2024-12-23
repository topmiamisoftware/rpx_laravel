<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetUpInvitation extends Model
{
    use HasFactory;

    protected $table = 'meet_up_invitations';
    protected $fillable = [];

    public function friendProfile() {
        return $this->hasOne('App\Models\SpotbieUser', 'id', 'friend_id');
    }

    public function meetUp() {
        return $this->belongsTo('App\Models\MeetUp', 'meet_up_id', 'id');
    }

    public function ownerAccount() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
