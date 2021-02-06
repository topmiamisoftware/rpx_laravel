<?php 

use App\Http\Controllers\Stream\StreamController;

use Illuminate\Support\Facades\Route;



Route::get('my-stream',         [StreamController::class, 'myStream']);

Route::get('my-general-stream', [StreamController::class, 'myGeneralStream'])->middleware('auth');