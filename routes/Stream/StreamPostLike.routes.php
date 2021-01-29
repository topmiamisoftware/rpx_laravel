<?php

use App\Http\Controllers\Stream\StreamPostLikeController;

use Illuminate\Support\Facades\Route;

Route::get('index',      [StreamPostLikeController::class, 'index']);

Route::post('insert',    [StreamPostLikeController::class, 'store'])->middleware('auth');

Route::get('show',       [StreamPostLikeController::class, 'show']);

Route::delete('destroy', [StreamPostLikeController::class, 'destroy'])->middleware('auth');