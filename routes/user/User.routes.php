<?php

    /* User Account Controllers */
    Route::post('user/sign_up', 'UserController@signUp');
    
    Route::post('user/check_user_auth', 'UserController@checkAuth');
    Route::post('user/login', 'UserController@logIn');

    Route::post('user/logout', 'UserController@logOut')->middleware('auth');

    Route::put('user/update', 'UserController@update')->middleware('auth');

    Route::post('user/settings', 'UserController@settings')->middleware('auth');

    Route::get('user/{user:username}', 'UserController@getUser');

    Route::delete('user/deactivate', 'UserController@deactivate')->middleware('auth');

    Route::post('user/send-pass-email', 'UserController@sendPassEmail');

    Route::put('user/complete-pass-reset', 'UserController@completePassReset');