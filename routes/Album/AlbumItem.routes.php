<?php

use App\Http\Controllers\Album\AlbumItemController;

use Illuminate\Support\Facades\Route;



Route::get('index',                [AlbumItemController::class, 'index']);

Route::get('show',                 [AlbumItemController::class, 'show']);

Route::get('{albumItem}/comments', [AlbumItemController::class, 'showComments']);    

Route::post('upload',              [AlbumItemController::class, 'uploadItems'])->middleware('auth');

Route::put('update',               [AlbumItemController::class, 'update'])->middleware('auth');

Route::delete('destroy',           [AlbumItemController::class, 'destroy'])->middleware('auth');

Route::delete('delete-all-unused', [AlbumItemController::class, 'deleteAllUnused'])->middleware('auth');
