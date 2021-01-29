<?php

namespace App\Http\Controllers\ProfileHeader;

use App\Http\Controllers\Controller;

use App\Models\ProfileHeader;

use Illuminate\Http\Request;

class ProfileHeaderController extends Controller
{
    
    public function myProfileHeader(ProfileHeader $profileHeader){
        return $profileHeader->myProfileHeader();
    } 

    public function setDefault(ProfileHeader $profileHeader, Request $request){    
        return $profileHeader->setDefault($request);
    }  

    public function uploadDefault(ProfileHeader $profileHeader, Request $request){    
        return $profileHeader->uploadDefault($request);
    } 

    public function uploadBackground(ProfileHeader $profileHeader, Request $request){        
        return $profileHeader->uploadBackground($request);
    }     
    
    public function deleteDefault(ProfileHeader $profileHeader, Request $request){        
        return $profileHeader->deleteDefault($request);
    }

    public function setDescription(ProfileHeader $profileHeader, Request $request){        
        return $profileHeader->setDescription($request);
    }  

}
