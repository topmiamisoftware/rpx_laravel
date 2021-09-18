<?php

use App\Http\Controllers\PlaceToEat\PlaceToEatController;

use Illuminate\Support\Facades\Route;

Route::post('verify', [PlaceToEatController::class, 'verify'])->middleware('auth');

Route::post('google', [PlaceToEatController::class, 'getGooglePlacesToEat'])->middleware('auth');
