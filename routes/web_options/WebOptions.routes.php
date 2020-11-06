<?php

    /* User Web Options Controllers */
    Route::get('web_options/show', 'WebOptionsController@show');

    Route::post('web_options/set_bg_color', 'WebOptionsController@setBgColor')->middleware('auth');

    Route::post('web_options/set_bg_image', 'WebOptionsController@setBgImage')->middleware('auth');