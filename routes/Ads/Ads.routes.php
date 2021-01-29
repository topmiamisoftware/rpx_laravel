<?php

use App\Http\Controllers\Ads\AdsController;

use Illuminate\Support\Facades\Route;

Route::post('get-single-ad-banner', [AdsController::class, 'getSingleAdBanner']);
