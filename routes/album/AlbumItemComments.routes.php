<?php

    /* Album Comment Controllers */
    Route::get('album_comments/index', 'AlbumItemCommentController@index');
    
    Route::get('album_comments/show', 'AlbumItemCommentController@show');

    Route::post('album_comments/create', 'AlbumItemCommentController@store')->middleware('auth');

    Route::put('album_comments/update', 'AlbumItemCommentController@update')->middleware('auth');

    Route::delete('album_comments/destroy', 'AlbumItemCommentController@destroy')->middleware('auth');

?>