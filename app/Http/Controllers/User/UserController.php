<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function signUp(Request $request, User $user)
    {
        return $user->signUp($request);
    }

    public function update(Request $request, User $user)
    {
        return $user->saveSettings($request);
    }

    public function settings(Request $request, User $user)
    {
        return $user->getSettings($request);
    }

    public function deactivate(User $user, Request $request)
    {
        return $user->deactivate($request);
    }

    public function logIn(User $user, Request $request){
        return $user->logIn($request);
    }

    public function logOut(User $user, Request $request){
        return $user->logOut($request);
    }

    public function closeBrowser(User $user, Request $request){
        return $user->closeBrowser($request);
    }

    public function myStream(User $user, Request $request){
        return $user->getMyStream($request);
    }

    public function checkAuth(User $user){
        return $user->checkIfLoggedIn();
    }

    public function confirmAccount(User $user, Request $request){
        return $user->confirmAccount($request);
    }

    public function getUser(User $user, Request $request){
        return $user->getUser($request);
    }

    public function sendPassEmail(User $user, Request $request){
        return $user->setPassResetPin($request);
    }

    public function completePassReset(User $user, Request $request){
        return $user->completePassReset($request);
    }

    public function changePassword(User $user, Request $request){
        return $user->changePassword($request);
    }

    public function uniqueEmail(User $user, Request $request){
        return $user->uniqueEmail($request);
    }

    public function sendCode(User $user, Request $request){
        return $user->sendCode($request);
    }

    public function checkConfirm(User $user, Request $request){
        return $user->sendCode($request);
    }

    public function facebookLogin(User $user, Request $request){
        return $user->facebookLogin($request);
    }   

    public function googleLogin(User $user, Request $request){
        return $user->googleLogin($request);
    } 

}   
