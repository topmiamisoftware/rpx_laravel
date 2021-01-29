<?php

use App\Http\Controllers\ExtraMedia\ExtraMediaController;

use Illuminate\Support\Facades\Route;


Route::get('index',      [ExtraMediaController::class, 'index']);

Route::post('save',      [ExtraMediaController::class, 'store'])->middleware('auth');

Route::post('upload',    [ExtraMediaController::class, 'upload'])->middleware('auth');

Route::put('update',     [ExtraMediaController::class, 'update'])->middleware('auth');

Route::get('show',       [ExtraMediaController::class, 'show']);

Route::delete('destroy', [ExtraMediaController::class, 'destroy'])->middleware('auth');