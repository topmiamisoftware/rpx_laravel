<?php

    /* Album Item Controllers */
    
    Route::get('album_items/index', 'AlbumItemController@index');
    Route::get('album_items/show', 'AlbumItemController@show');

    Route::get('album_items/{albumItem}/comments', 'AlbumItemController@showComments');    

    Route::post('album_items/upload', 'AlbumItemController@uploadItems')->middleware('auth');

    Route::put('album_items/update', 'AlbumItemController@update')->middleware('auth');

    Route::delete('album_items/destroy', 'AlbumItemController@destroy')->middleware('auth');
    Route::delete('album_items/delete_all_unused', 'AlbumItemController@deleteAllUnused')->middleware('auth');

?>