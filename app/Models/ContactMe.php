<?php

namespace App\Models;

use Auth;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMe extends Model
{

    use HasFactory, SoftDeletes;

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }       

    public function getContactMe(Request $request, User $user){

        $contactMe = $this->select('facebook', 'instagram', 'twitter', 'whatsapp', 'snapchat')
        ->where('user_id', $user->id)
        ->first();

        $contactMeResponse = array(
            'message' => 'success',
            'contact_me' => $contactMe
        );  

        return response($contactMeResponse);

    }

    public function saveContactMe(Request $request){
        
        $validatedData = $request->validate([
            'facebook' => ['nullable', 'string', 'max:135'],
            'instagram' => ['nullable', 'string', 'max:135'],
            'twitter' => ['nullable', 'string', 'max:135'],
            'whatsapp' => ['nullable', 'string', 'max:135'],
            'snapchat' => ['nullable', 'string', 'max:135']
        ]);

        $user = Auth::user();

        $user->contactMe->facebook = $validatedData['facebook'];
        $user->contactMe->instagram = $validatedData['instagram'];
        $user->contactMe->twitter = $validatedData['twitter'];
        $user->contactMe->whatsapp = $validatedData['whatsapp'];
        $user->contactMe->snapchat = $validatedData['snapchat'];
        
        $user->contactMe->save();

        $contactMeResponse = array(
            'message' => 'success'
        );    
        
        return response($contactMeResponse);

    }

    public function createContactMe(User $user){

        $contactMe = new ContactMe();
        $contactMe->user_id = $user->id;
        $contactMe->save();        

    }

}
