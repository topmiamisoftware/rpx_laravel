<?php

    /* Extra Media Controllers */
    Route::get('extra_media/index', 'ExtraMediaController@index');

    Route::post('extra_media/save', 'ExtraMediaController@store')->middleware('auth');
    Route::post('extra_media/upload', 'ExtraMediaController@upload')->middleware('auth');

    Route::put('extra_media/update', 'ExtraMediaController@update')->middleware('auth');

    Route::get('extra_media/show', 'ExtraMediaController@show');

    Route::delete('extra_media/destroy', 'ExtraMediaController@destroy')->middleware('auth');

?>