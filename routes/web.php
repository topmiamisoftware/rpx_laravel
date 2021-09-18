<?php

    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::prefix('user')                       ->group(__DIR__ . '/User/User.routes.php');

    Route::prefix('album')                      ->group(__DIR__ . '/Album/Album.routes.php');
    Route::prefix('album-item')                 ->group(__DIR__ . '/Album/AlbumItem.routes.php');
    Route::prefix('album-item-comment')         ->group(__DIR__ . '/Album/AlbumItemComments.routes.php');
    Route::prefix('album-item-like')            ->group(__DIR__ . '/Album/AlbumItemLikes.routes.php');

    Route::prefix('chat')                       ->group(__DIR__ . '/Chat/Chat.routes.php');

    Route::prefix('contact-me')                 ->group(__DIR__ . '/ContactMe/ContactMe.routes.php');

    Route::prefix('friendship')                 ->group(__DIR__ . '/Friendship/Friendship.routes.php');

    Route::prefix('stream')                     ->group(__DIR__ . '/Stream/Stream.routes.php');
    Route::prefix('stream')                     ->group(__DIR__ . '/Stream/StreamPost.routes.php');
    Route::prefix('stream')                     ->group(__DIR__ . '/Stream/StreamPostComment.routes.php');
    Route::prefix('stream')                     ->group(__DIR__ . '/Stream/StreamPostLike.routes.php');

    Route::prefix('extra-media')                ->group(__DIR__ . '/ExtraMedia/ExtraMedia.routes.php');

    Route::prefix('public-stats')               ->group(__DIR__ . '/PublicStats/PublicStats.routes.php');

    Route::prefix('web-options')                ->group(__DIR__ . '/WebOptions/WebOptions.routes.php');

    Route::prefix('profile-header')             ->group(__DIR__ . '/ProfileHeader/ProfileHeader.routes.php');

    Route::prefix('user-location')              ->group(__DIR__ . '/UserLocation/UserLocation.routes.php');

    Route::prefix('surroundings')               ->group(__DIR__ . '/Surroundings/SurroundingApi.routes.php');

    Route::prefix('my-favorites')               ->group(__DIR__ . '/MyFavorites/MyFavorites.routes.php');

    Route::prefix('ads')                        ->group(__DIR__ . '/Ads/Ads.routes.php');

    Route::prefix('bugs')                       ->group(__DIR__ . '/Bugs/Bugs.routes.php');

    Route::prefix('place-to-eat')               ->group(__DIR__ . '/PlaceToEat/PlaceToEat.routes.php');

    Route::prefix('place-to-eat-item')         ->group(__DIR__ . '/PlaceToEatItems/PlaceToEatItems.routes.php');

    Route::prefix('loyalty-points')             ->group(__DIR__ . '/LoyaltyPoints/loyalty-points.routes.php');    