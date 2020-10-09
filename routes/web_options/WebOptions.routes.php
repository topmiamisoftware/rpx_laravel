<?php

    /* User Web Options Controllers */
    Route::get('web_options/show', 'WebOptionsController@show');

    Route::post('web_options/set_bg_color', 'WebOptionsController@setBgColor');

    Route::post('web_options/set_bg_image', 'WebOptionsController@setBgImage');

?>