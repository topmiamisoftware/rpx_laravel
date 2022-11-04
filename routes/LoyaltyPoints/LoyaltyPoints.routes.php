<?php

use App\Http\Controllers\LoyaltyPointBalance\LoyaltyPointBalanceController;
use App\Http\Controllers\LoyaltyPointBalance\LoyaltyPointBalanceAggregatorController;

use Illuminate\Support\Facades\Route;

Route::post('show', [LoyaltyPointBalanceAggregatorController::class, 'show'])->middleware('auth');
Route::post('store', [LoyaltyPointBalanceController::class, 'store'])->middleware('auth');
Route::post('reset', [LoyaltyPointBalanceController::class, 'reset'])->middleware('auth');
Route::get('ledger', [LoyaltyPointBalanceController::class, 'index'])->middleware('auth');
