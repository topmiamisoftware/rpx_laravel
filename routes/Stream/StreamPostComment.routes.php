<?php

use App\Http\Controllers\Stream\StreamPostCommentController;

use Illuminate\Support\Facades\Route;



Route::get('index',      [StreamPostCommentController::class, 'index']);

Route::post('create',    [StreamPostCommentController::class, 'store'])->middleware('auth');

Route::put('update',     [StreamPostCommentController::class, 'update'])->middleware('auth');

Route::get('show',       [StreamPostCommentController::class, 'show']);

Route::delete('destroy', [StreamPostCommentController::class, 'destroy'])->middleware('auth');