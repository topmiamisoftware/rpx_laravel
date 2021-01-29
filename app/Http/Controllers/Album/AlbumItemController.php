<?php

namespace App\Http\Controllers\Album;

use App\Http\Controllers\Controller;

use App\Models\AlbumItem;

use Illuminate\Http\Request;

class AlbumItemController extends Controller
{

    public function index()
    {
        //will return user info for display

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function showComments(AlbumItem $albumItem, Request $request)
    {
        return $albumItem->showComments($request);
    }

    public function edit(AlbumItem $albumItem)
    {
        //
    }

    public function update(Request $request, AlbumItem $albumItem)
    {
        //
    }

    public function destroy(AlbumItem $albumItem)
    {
        //
    }

    public function deleteAllUnused(AlbumItem $albumItem){

    }

}
