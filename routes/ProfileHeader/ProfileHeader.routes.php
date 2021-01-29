<?php

use App\Http\Controllers\ProfileHeader\ProfileHeaderController;

use Illuminate\Support\Facades\Route;

Route::post('my-profile-header', [ProfileHeaderController::class, 'myProfileHeader'])->middleware('auth');

Route::patch('set-default',      [ProfileHeaderController::class, 'setDefault'])->middleware('auth');

Route::patch('set-description',  [ProfileHeaderController::class, 'setDescription'])->middleware('auth');

Route::post('upload-default',    [ProfileHeaderController::class, 'uploadDefault'])->middleware('auth');

Route::post('upload-background', [ProfileHeaderController::class, 'uploadBackground'])->middleware('auth');

Route::delete('delete-default',  [ProfileHeaderController::class, 'deleteDefault'])->middleware('auth');