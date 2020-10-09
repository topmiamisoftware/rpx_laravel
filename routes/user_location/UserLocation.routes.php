<?php


    Route::get('user_location/get_current_location', 'UserLocationController@show')->middleware('auth');

    Route::post('user_location/save_current_location', 'UserLocationController@saveCurrentLocation')->middleware('auth');
    
    Route::post('user_location/retrieve_surroundings', 'UserLocationController@retrieveSurroundings');

?>