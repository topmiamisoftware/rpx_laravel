<?php

use App\Http\Controllers\RedeemableItems\RedeemableItemsController;

use Illuminate\Support\Facades\Route;

Route::post('create', [RedeemableItemsController::class, 'create'])->middleware('auth');

Route::post('redeem',   [RedeemableItemsController::class, 'redeem'])->middleware('auth');

Route::post('index',   [RedeemableItemsController::class, 'index'])->middleware('auth');