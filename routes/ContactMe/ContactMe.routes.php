<?php

use App\Http\Controllers\ContactMe\ContactMeController;

use Illuminate\Support\Facades\Route;


Route::post('update',     [ContactMeController::class, 'update'])->middleware('auth');

Route::get('{user}/show', [ContactMeController::class, 'show']);