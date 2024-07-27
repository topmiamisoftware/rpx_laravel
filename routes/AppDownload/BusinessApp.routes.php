<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AppVersionController;

Route::get('download', [AppVersionController::class, 'download'])->middleware('auth');
Route::put('check', [AppVersionController::class, 'check'])->middleware('auth');

?>
