<?php

namespace App\Models;

use Auth;
use Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DefaultImages;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileHeader extends User
{
    
    use SoftDeletes;

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

        $validatedData = $request->validate([
            'default_picture' => 'required|max:150|min:1'
        ]);

        $user = Auth::user();
        
        $user->spotbieUser->default_picture = $validatedData['default_picture'];
        
        $user->spotbieUser->save();
        
        $response = array(
            'success' => true,
            'default_picture' => $user->spotbieUser->default_picture
        );

        return response($response);

    }

    public function uploadDefault(Request $request){

        $success = true;
        $message = null;

        $validatedData = $request->validate([
            'default_picture' => 'required|image|max:25000'
        ]);

        $user = Auth::user();
        
        $defaultImagesPath = config('spotbie.default_images_path');
        $hashedFileName = $validatedData['default_picture']->hashName();

        $user->spotbieUser->default_picture = $defaultImagesPath . $user->id. '/' . $hashedFileName;
        
        $newFile = Image::make($request->file('default_picture'))->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $newFile = $newFile->encode('jpg', 60);
        $newFile = (string) $newFile;

        Storage::put( 'defaults/' . $user->id. '/' . $hashedFileName, $newFile);
        
        $user->spotbieUser->save();
        
        $userDefaultList = $user->defaultImages;

        if(count($userDefaultList) == 10){

            $success = false;
            $message = 'You have already uploaded 10 default images. Delete one before uploading another one.';

        } else {

            $defaultImage = new DefaultImages;
            $defaultImage->user_id = $user->id;
            $defaultImage->default_image_url = $user->spotbieUser->default_picture;

            $defaultImage->save();

        }

        $response = array(
            'success' => $success,
            'message' => $message,
            'default_picture' => $user->spotbieUser->default_picture
        );

        return response($response);

    }

    public function deleteDefault(Request $request){

        $validatedData = $request->validate([
            'default_image_url' => 'required|string'
        ]);

        $user = Auth::user();

        $user->defaultImages()
        ->select('user_id', 'default_image_url')
        ->where('user_id', $user->id)
        ->where('default_image_url', $validatedData['default_image_url'])
        ->delete();

        $newDefault = $user->defaultImages()
        ->select('user_id', 'default_image_url')
        ->where('user_id', $user->id)
        ->orderBy('id', 'asc')
        ->first();

        if($newDefault !== null)
            $user->spotbieUser->default_picture = $newDefault->default_image_url; 
        else
            $user->spotbieUser->default_picture = config('spotbie.default_images_path').'user.png';

        $user->spotbieUser->save();

        $response = array(
            'success' => true,
            'new_profile_default' => $user->spotbieUser->default_picture
        );

        return response($response);

    }

    public function setDescription(Request $request){

        $validatedData = $request->validate([
            'description' => 'required|string|max:500|min:1'
        ]);

        $user = Auth::user();
        
        $user->spotbieUser->description = $validatedData['description'];
        
        $user->spotbieUser->save();
        
        $response = array(
            'description' =>  $user->spotbieUser->description
        );

        return response($response);

    }

    public function uploadBackground(Request $request){

        $validatedData = $request->validate([
            'background_picture' => 'required|image|max:25000'
        ]);

        $user = Auth::user();
        
        $backgroundImagePath = config('spotbie.background_images_path');
        $hashedFileName = $validatedData['background_picture']->hashName();
        
        //Let's delete any other backgrounds
        if(Storage::exists('backgrounds/'.$user->id)){
            $allFiles = Storage::allFiles('backgrounds/'.$user->id);
            Storage::delete($allFiles); 
        }

        $user->webOptions->spotmee_bg = $backgroundImagePath . $user->id. '/' . $hashedFileName;
        
        $newFile = Image::make($request->file('background_picture'))->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $newFile = $newFile->encode('jpg', 60);
        $newFile = (string) $newFile;

        Storage::put( 'backgrounds/' . $user->id. '/' . $hashedFileName, $newFile);
        
        $user->webOptions->save();

        $response = array(
            'success' => true,
            'background_picture' => $user->webOptions->spotmee_bg
        );

        return response($response);

    }

}
