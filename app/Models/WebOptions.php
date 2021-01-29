<?php

namespace App\Models;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebOptions extends Model
{

    use HasFactory, SoftDeletes;

    /* 
    * WebOptions belogns to User.
    */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    public function getWebOptions(){

        $user = Auth::user();

        $webOptions = $user->webOptions;

        $response = array(
            'bg_color' => $webOptions['bg_color'],
            'spotmee_bg' => $webOptions['spotmee_bg']
        );

        return response($response);    

    }

    public function setBgColor(Request $request){

        $validatedData = $request->validate([
            'color' => 'required|max:7|min:1'
        ]);

        $user = Auth::user();

        $webOptions = $user->webOptions;

        $webOptions->bg_color = $validatedData['color'];

        $webOptions->save();

        $response = array(
            'message' => 'success',
            'bg_color' => $webOptions['bg_color'],
        );

        return response($response);    

    }

    public function setBgImage(Request $request){

        $validatedData = $request->validate([
            'color' => 'required|max:7|min:1'
        ]);

        $user = Auth::user();

        $webOptions = $user->webOptions;

        $webOptions->bg_color = $validatedData['color'];

        $webOptions->save();

        $response = array(
            'message' => 'success',
            'bg_color' => $webOptions['bg_color'],
        );

        return response($response);    

    }

    public function createWebOptions(User $user){

        $webOptions = new WebOptions();
        $webOptions->user_id = $user->id;

        $webOptions->save();

    }

}
