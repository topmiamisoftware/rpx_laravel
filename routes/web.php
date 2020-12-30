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

    require 'user/User.routes.php';

    require 'album/Album.routes.php';
    require 'album/AlbumItem.routes.php';
    require 'album/AlbumItemComments.routes.php';
    require 'album/AlbumItemLikes.routes.php';

    require 'chat/Chat.routes.php';

    require 'friendship/Friendship.routes.php';

    require 'stream/StreamPost.routes.php';
    require 'stream/StreamPostComment.routes.php';
    require 'stream/StreamPostLike.routes.php';

    require 'stream/ExtraMedia.routes.php';

    require 'public_stats/PublicStats.routes.php';

    require 'web_options/WebOptions.routes.php';

    require 'profile_header/ProfileHeader.routes.php';

    require 'user_location/UserLocation.routes.php';

    require 'surroundings_api/SurroundingApi.routes.php';

    require 'my_favorites/MyFavorites.routes.php';

    require 'ads/Ads.routes.php';
    
    require 'bugs/Bugs.routes.php';
