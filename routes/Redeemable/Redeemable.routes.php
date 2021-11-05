<?php

use App\Http\Controllers\RedeemableLoyaltyPoint\RedeemableLoyaltyPointController;

use Illuminate\Support\Facades\Route;

Route::post('create', [RedeemableLoyaltyPointController::class, 'create'])->middleware('auth');

Route::post('redeem',   [RedeemableLoyaltyPointController::class, 'redeem'])->middleware('auth');

Route::post('index',   [RedeemableLoyaltyPointController::class, 'index'])->middleware('auth');