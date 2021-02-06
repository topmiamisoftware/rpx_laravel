<?php

use App\Http\Controllers\PublicStats\PublicStatsController;

use Illuminate\Support\Facades\Route;

Route::get('total-users', [PublicStatsController::class, 'getTotalUsers']);
