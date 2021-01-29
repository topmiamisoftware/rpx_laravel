<?php

use App\Http\Controllers\WebOptions\WebOptionsController;

use Illuminate\Support\Facades\Route;



Route::get('show',          [WebOptionsController::class, 'show']);

Route::post('set-bg-color', [WebOptionsController::class, 'setBgColor'])->middleware('auth');

Route::post('set-bg-image', [WebOptionsController::class, 'setBgImage'])->middleware('auth');