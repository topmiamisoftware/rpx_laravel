<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Sms extends Model
{
    use HasFactory;

    public $table = 'sms';



    public function fromUser()
    {
        return $this->belongsTo('App\Models\User', 'from_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\Models\User', 'to_id');
    }

    public function createNewSms(string $smsText, User $user, Business $business)
    {
        $spotbieUser = $user->spotbieUser()->first();
        $phoneNumber = $spotbieUser->phone_number;

        $sms = new Sms;

        if (is_null($phoneNumber)) {
            return $sms;
        }

        $businessUserId = $business->user()->first()->id;

        $sms->body = $smsText;
        $sms->price = 0.0079;
        $sms->to_id = $user->id;
        $sms->from_id = $businessUserId;
        $sms->to_phone = $phoneNumber;
        $sms->save();
        $sms->refresh();

        return $sms;
    }
}
