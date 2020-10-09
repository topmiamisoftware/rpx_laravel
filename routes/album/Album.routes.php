<?php

    /* Album Controllers */
    Route::get('album/my_albums', 'AlbumController@myAlbums')->middleware('auth');

    Route::get('album/{album}', 'AlbumController@viewAlbum');

    Route::get('album/{album}/slide_show_set', 'AlbumController@slideShowSet');

    Route::post('album/create', 'AlbumController@store')->middleware('auth');

    Route::put('album/update', 'AlbumController@update')->middleware('auth');
    
    Route::delete('album/destroy', 'AlbumController@destroy')->middleware('auth');

?>