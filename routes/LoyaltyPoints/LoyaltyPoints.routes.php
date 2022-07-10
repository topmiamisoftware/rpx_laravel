<?php

use App\Http\Controllers\LoyaltyPointBalance\LoyaltyPointBalanceController;

use Illuminate\Support\Facades\Route;

Route::post('show', [LoyaltyPointBalanceController::class, 'show'])->middleware('auth');
Route::post('store', [LoyaltyPointBalanceController::class, 'store'])->middleware('auth');
Route::post('reset', [LoyaltyPointBalanceController::class, 'reset'])->middleware('auth');
Route::get('ledger', [LoyaltyPointBalanceController::class, 'index'])->middleware('auth');
