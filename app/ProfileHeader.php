<?php

namespace App;

use Auth;
use Illuminate\Http\Request;

class ProfileHeader extends User
{
    //

    public function myProfileHeader(){
        
        $user = Auth::user();

        $user_info = array(
            'username' => $user->username
        );

        $default_images = $user->defaultImages()
        ->select('default_image_url')
        ->orderBy('created_at')
        ->get();

        $stream_by_info = $user->spotbieUser()
        ->select('default_picture', 'first_name', 'last_name', 'description', 'animal', 'ghost_mode', 'privacy')
        ->first();

        $web_options = $user->webOptions()
        ->select('bg_color')
        ->first();

        $profile_header_response = array(
            "user" => $user_info,
            "default_images" => $default_images,
            "spotbie_user" => $stream_by_info,
            "web_options" => $web_options
        );

        return response($profile_header_response);

    }
    
    public function setDefault(Request $request){

        $validatedDate = $request->validate([
            'default_picture' => 'required|max:100|min:1'
        ]);

        $user = Auth::user();
        
        $user->spotbieUser->default_picture = $validatedDate['default_picture'];
        
        $user->spotbieUser->save();
        
        $response = array(
            'message' => 'success',
            'default_picture' => $user->spotbieUser->default_picture
        );

        return response($response);

    }

    public function uploadDefault(Request $request){

        $validatedDate = $request->validate([
            'default_picture' => 'required|image|max:2999'
        ]);

        $user = Auth::user();

        $user->spotbieUser->default_picture = $user->id. '/' .$validatedDate['default_picture']->hashName();
        
        $validatedDate['default_picture']->storeAs('defaults', $user->spotbieUser->default_picture);
        
        $user->spotbieUser->save();
        
        $response = array(
            'message' => 'success',
            'default_picture' => $user->spotbieUser->default_picture
        );

        return response($response);

    }

    public function setDescription(Request $request){

        $validatedDate = $request->validate([
            'description' => 'required|max:500|min:1'
        ]);

        $user = Auth::user();
        
        $user->spotbieUser->description = $validatedDate['description'];
        
        $user->spotbieUser->save();
        
        $response = array(
            'description' =>  $user->spotbieUser->description
        );

        return response($response);

    }

}
