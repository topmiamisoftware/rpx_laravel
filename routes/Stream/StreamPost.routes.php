<?php

use App\Http\Controllers\Stream\StreamPostController;

use Illuminate\Support\Facades\Route;



Route::post('create', [StreamPostController::class, 'store'])->middleware('auth');

Route::put('update', [StreamPostController::class, 'update'])->middleware('auth');

Route::get('show', [StreamPostController::class, 'show']);

Route::delete('destroy', [StreamPostController::class, 'destroy'])->middleware('auth');

