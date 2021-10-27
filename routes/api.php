<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('user')                       ->group(__DIR__ . '/User/User.routes.php');

Route::prefix('user-location')              ->group(__DIR__ . '/UserLocation/UserLocation.routes.php');

Route::prefix('surroundings')               ->group(__DIR__ . '/Surroundings/SurroundingApi.routes.php');

Route::prefix('in-house')                   ->group(__DIR__ . '/Ads/Ads.routes.php');

Route::prefix('bugs')                       ->group(__DIR__ . '/Bugs/Bugs.routes.php');

Route::prefix('business')                   ->group(__DIR__ . '/Business/Business.routes.php');

Route::prefix('reward')                     ->group(__DIR__ . '/Reward/Reward.routes.php');

Route::prefix('my-favorites')               ->group(__DIR__ . '/MyFavorites/MyFavorites.routes.php');

Route::prefix('loyalty-points')             ->group(__DIR__ . '/LoyaltyPoints/loyalty-points.routes.php');    

Route::prefix('redeemable')                 ->group(__DIR__ . '/Redeemable/redeemable.routes.php');    