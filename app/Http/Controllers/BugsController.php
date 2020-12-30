<?php

namespace App\Http\Controllers;

use App\Bugs;
use Illuminate\Http\Request;

class BugsController extends Controller
{
    public function insert(Request $bugRequest, Bugs $bugs){

        return $bugs->insert($bugRequest);

    }
}
