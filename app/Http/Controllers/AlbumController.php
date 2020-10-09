<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    
    public function myAlbums(Album $album){
        return $album->myAlbums();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function viewAlbum(Album $album, Request $request)
    {
        return $album->viewAlbum($request);
    }

    public function slideShowSet(Album $album, Request $request)
    {
        return $album->slideShowSet($request);
    }
    
    public function update(Request $request, Album $albums)
    {
        //
    }

    public function destroy(Album $albums)
    {
        //
    }
}
