<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    
    public function myAlbums(Album $album){
        return $album->myAlbums();
    }


    public function viewAlbum(Album $album, Request $request)
    {
        return $album->viewAlbum($request);
    }

    public function slideShowSet(Album $album, Request $request)
    {
        return $album->slideShowSet($request);
    }
    
    
}
