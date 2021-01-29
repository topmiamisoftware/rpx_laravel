<?php

use App\Http\Controllers\Album\AlbumItemLikeController;

use Illuminate\Support\Facades\Route;



Route::post('index',           [AlbumItemLikeController::class, 'index']);

Route::post('like-album-item', [AlbumItemLikeController::class, 'likeAlbumItem']);