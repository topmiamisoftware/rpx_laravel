<?php

use App\Http\Controllers\CustomerManager;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomerManager::class, 'index'])->middleware('auth');
