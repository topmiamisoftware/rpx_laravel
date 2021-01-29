<?php 

use App\Http\Controllers\StreamPost\StreamController;

use Illuminate\Support\Facades\Route;



Route::get('my_stream',         [StreamController::class, 'myStream']);

Route::get('my_general_stream', [StreamController::class, 'myGeneralStream'])->middleware('auth');