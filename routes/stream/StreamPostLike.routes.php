<?php

    /* Stream Post Like Controllers */
    Route::get('stream_post_like/index', 'StreamPostLikeController@index');
    
    Route::post('stream_post_like/insert', 'StreamPostLikeController@store')->middleware('auth');

    Route::get('stream_post_like/show', 'StreamPostLikeController@show');

    Route::delete('stream_post_like/destroy', 'StreamPostLikeController@destroy')->middleware('auth');