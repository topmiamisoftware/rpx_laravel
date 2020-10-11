<?php

    Route::get('profile_header/my_profile_header', 'ProfileHeaderController@myProfileHeader')->middleware('auth');

    Route::patch('profile_header/set_default', 'ProfileHeaderController@setDefault')->middleware('auth');
    Route::patch('profile_header/set_description', 'ProfileHeaderController@setDescription')->middleware('auth');

    Route::post('profile_header/upload_default', 'ProfileHeaderController@uploadDefault')->middleware('auth');

    Route::post('profile_header/upload_background', 'ProfileHeaderController@uploadBackground')->middleware('auth');

    Route::delete('profile_header/delete_default', 'ProfileHeaderController@deleteDefault')->middleware('auth');

    Route::post('contact_me/update', 'ContactMeController@update')->middleware('auth');

    Route::get('contact_me/{user}/show', 'ContactMeController@show');

?>