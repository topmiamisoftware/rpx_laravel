<?php

namespace App\Http\Controllers\Album;

use App\Http\Controllers\Controller;

use App\Models\AlbumItemComment;

use Illuminate\Http\Request;

class AlbumItemCommentController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, AlbumItemComment $albumItemComment)
    {
    }

    public function update(Request $request, AlbumItemComment $albumItemComment)
    {
        //
    }

    public function destroy(AlbumItemComment $albumItemComment, Request $request)
    {
        return $albumItemComment->deleteComment($request);
    }

    public function addAlbumMediaComment(AlbumItemComment $albumItemComment, Request $request){
        return $albumItemComment->addAlbumMediaComment($request);
    }

}
