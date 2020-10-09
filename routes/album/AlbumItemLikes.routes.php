<?php

    /* Album Like Controllers */
    Route::post('album_likes/index', 'AlbumItemLikeController@index');

    Route::post('album_likes/like_album_item', 'AlbumItemLikeController@likeAlbumItem');


?>