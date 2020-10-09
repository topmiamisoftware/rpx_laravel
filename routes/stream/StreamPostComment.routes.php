<?php

    /* Stream Post Comment Controllers */
    Route::get('stream_post_comment/index', 'StreamPostCommentController@index');

    Route::post('stream_post_comment/create', 'StreamPostCommentController@store')->middleware('auth');

    Route::put('stream_post_comment/update', 'StreamPostCommentController@update')->middleware('auth');

    Route::get('stream_post_comment/show', 'StreamPostCommentController@show');

    Route::delete('stream_post_comment/destroy', 'StreamPostCommentController@destroy')->middleware('auth');

?>