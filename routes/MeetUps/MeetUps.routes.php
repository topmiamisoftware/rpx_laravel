<?php

use App\Http\Controllers\MeetUpController;
use Illuminate\Support\Facades\Route;

Route::get('index', [MeetUpController::class, 'index'])->middleware('auth');
Route::get('show/{id}', [MeetUpController::class, 'show'])->middleware('auth');
Route::delete('', [MeetUpController::class, 'destroy'])->middleware('auth');
Route::post('', [MeetUpController::class, 'createMeetUp'])->middleware('auth');
Route::put('{id}', [MeetUpController::class, 'editMeetUp'])->middleware('auth');
