<?php

use App\Http\Controllers\User\UserController;

use Illuminate\Support\Facades\Route;



Route::post('sign-up',            [UserController::class, 'signUp']);

Route::post('check-user-auth',    [UserController::class, 'checkAuth']);

Route::post('login',              [UserController::class, 'logIn']);

Route::post('logout',             [UserController::class, 'logOut'])->middleware('auth');

Route::put('update',              [UserController::class, 'update'])->middleware('auth');

Route::post('settings',           [UserController::class, 'settings'])->middleware('auth');

Route::get('{user:username}',     [UserController::class, 'getUser']);

Route::delete('deactivate',       [UserController::class, 'deactivate'])->middleware('auth');

Route::put('complete-pass-reset', [UserController::class, 'completePassReset']);

Route::put('change-password',     [UserController::class, 'changePassword']);