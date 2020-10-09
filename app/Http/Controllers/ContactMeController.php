<?php

namespace App\Http\Controllers;

use App\ContactMe;
use App\User;
use Illuminate\Http\Request;

class ContactMeController extends Controller
{

    public function show(Request $request, ContactMe $contactMe, User $user)
    {
        return $contactMe->getContactMe($request, $user);
    }

    public function update(Request $request, ContactMe $contactMe)
    {
        return $contactMe->saveContactMe($request);
    }

}
