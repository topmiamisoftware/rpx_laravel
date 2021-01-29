<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;

use App\Models\Stream;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    
    public function myStream(Stream $stream, Request $request){
        return $stream->myStream($request);
    }

    public function myGeneralStream(Stream $stream, Request $request){
        return $stream->myGeneralStream($request);
    }
    
}
