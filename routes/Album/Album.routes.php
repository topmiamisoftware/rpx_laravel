<?php

use App\Http\Controllers\Album\AlbumController;

use Illuminate\Support\Facades\Route;



Route::post('my-albums',             [AlbumController::class, 'myAlbums'])->middleware('auth');

Route::post('public-albums',         [AlbumController::class, 'publicAlbums']);

Route::get('{album}',                [AlbumController::class, 'viewAlbum']);

Route::get('{album}/slide-show-set', [AlbumController::class, 'slideShowSet']);

Route::post('create',                [AlbumController::class, 'store'])->middleware('auth');

Route::put('update',                 [AlbumController::class, 'update'])->middleware('auth');

Route::delete('destroy',             [AlbumController::class, 'destroy'])->middleware('auth');