<?php

namespace App\Http\Controllers\ContactMe;

use App\Http\Controllers\Controller;

use App\Models\ContactMe;
use App\Models\User;
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
