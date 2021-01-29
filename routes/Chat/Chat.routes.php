<?php

use App\Http\Controllers\Chat\ChatController;

use Illuminate\Support\Facades\Route;



Route::get('index',             [ChatController::class, 'index'])->middleware('auth');

Route::post('insert',           [ChatController::class, 'store'])->middleware('auth');

Route::put('update',            [ChatController::class, 'updateMessage'])->middleware('auth');

Route::get('show',              [ChatController::class, 'show'])->middleware('auth');

Route::delete('destroy-all',    [ChatController::class, 'deleteAll'])->middleware('auth');

Route::delete('destroy-single', [ChatController::class, 'deleteSingle'])->middleware('auth');
