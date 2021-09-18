<?php

use App\Http\Controllers\PlaceToEatItem\PlaceToEatItemController;

use Illuminate\Support\Facades\Route;

Route::post('create', [PlaceToEatItemController::class, 'create'])->middleware('auth');

Route::post('index', [PlaceToEatItemController::class, 'index'])->middleware('auth');

Route::post('update', [PlaceToEatItemController::class, 'update'])->middleware('auth');

Route::post('delete', [PlaceToEatItemController::class, 'delete'])->middleware('auth');

Route::post('upload-media', [PlaceToEatItemController::class, 'uploadMedia'])->middleware('auth');

