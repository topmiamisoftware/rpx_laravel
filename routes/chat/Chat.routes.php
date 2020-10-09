<?php

    /* Chat Controllers */
    Route::get('chat/index', 'ChatController@index')->middleware('auth');

    Route::post('chat/insert', 'ChatController@store')->middleware('auth');

    Route::put('chat/update', 'ChatController@updateMessage')->middleware('auth');

    Route::get('chat/show', 'ChatController@show')->middleware('auth');

    Route::delete('chat/destroy_all', 'ChatController@deleteAll')->middleware('auth');

    Route::delete('chat/destroy_single', 'ChatController@deleteSingle')->middleware('auth');


?>