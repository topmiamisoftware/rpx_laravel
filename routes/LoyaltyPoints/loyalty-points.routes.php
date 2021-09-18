<?php

use App\Http\Controllers\LoyaltyPointBalance\LoyaltyPointBalanceController;

use Illuminate\Support\Facades\Route;

Route::post('show', [LoyaltyPointBalanceController::class, 'show'])->middleware('auth');

Route::post('store', [LoyaltyPointBalanceController::class, 'store'])->middleware('auth');

Route::post('add', [LoyaltyPointBalanceController::class, 'add'])->middleware('auth');