<?php

    Route::get('get-classifications', 'SurroundingsApiController@getClassifications');

    Route::post('search-events', 'SurroundingsApiController@searchEvents');

    Route::post('search-businesses', 'SurroundingsApiController@searchBusinesses');

    Route::post('pull-info-object', 'SurroundingsApiController@pullInfoObject');