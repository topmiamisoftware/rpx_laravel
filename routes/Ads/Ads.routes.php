<?php

use App\Http\Controllers\Ads\AdsController;

use Illuminate\Support\Facades\Route;

Route::post('header-banner', [AdsController::class, 'headerBanner']);

Route::post('single-ad-list', [AdsController::class, 'singleAdList']);

Route::post('create', [AdsController::class, 'create'])->middleware('auth');

Route::post('update', [AdsController::class, 'updateModel'])->middleware('auth');

Route::post('delete', [AdsController::class, 'deleteModel'])->middleware('auth');

Route::post('index', [AdsController::class, 'index'])->middleware('auth');

