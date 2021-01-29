<?php

namespace App\Http\Controllers\Album;

use App\Http\Controllers\Controller;

use App\Models\Album;

use Illuminate\Http\Request;

class AlbumController extends Controller
{
    
    public function myAlbums(Album $album)
    {
        return $album->myAlbums();
    }

    public function publicAlbums(Album $album, Request $request)
    {
        return $album->publicAlbums($request);
    }

    public function viewAlbum(Album $album, Request $request)
    {
        return $album->viewAlbum($request);
    }

    public function slideShowSet(Album $album, Request $request)
    {
        return $album->slideShowSet($request);
    }
    
    public function update(Album $album, Request $request)
    {
        return $album->update($request);
    }
    
}
