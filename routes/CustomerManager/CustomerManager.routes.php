<?php

use App\Http\Controllers\CustomerManager;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomerManager::class, 'index'])->middleware('auth');
Route::post('sms', [CustomerManager::class, 'sms'])->middleware('auth');
Route::get('sms-group-list', [CustomerManager::class, 'smsGroupList'])->middleware('auth');
