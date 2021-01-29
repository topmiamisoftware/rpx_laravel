<?php

use App\Http\Controllers\Album\AlbumItemCommentController;

use Illuminate\Support\Facades\Route;



Route::post('index',     [AlbumItemCommentController::class, 'index']);

Route::post('show',      [AlbumItemCommentController::class, 'show']);

Route::post('create',    [AlbumItemCommentController::class, 'store'])->middleware('auth');

Route::put('update',     [AlbumItemCommentController::class, 'update'])->middleware('auth');

Route::delete('destroy', [AlbumItemCommentController::class, 'destroy'])->middleware('auth');
