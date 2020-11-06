<?php

    /* Stream Post Controllers */

    Route::post('stream_post/create', 'StreamPostController@store')->middleware('auth');

    Route::put('stream_post/update', 'StreamPostController@update')->middleware('auth');

    Route::get('stream_post/show', 'StreamPostController@show');

    Route::delete('stream_post/destroy', 'StreamPostController@destroy')->middleware('auth');

    Route::get('stream/my_stream', 'StreamController@myStream');

    Route::get('stream/my_general_stream', 'StreamController@myGeneralStream')->middleware('auth');