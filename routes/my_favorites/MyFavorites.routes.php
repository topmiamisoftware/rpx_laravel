<?php

    Route::get('my-favorites', 'MyFavoritesController@getFavorites')->middleware('auth');

    Route::post('save-favorite', 'MyFavoritesController@saveFavorite')->middleware('auth');

    Route::delete('remove-favorite', 'MyFavoritesController@removeFavorite')->middleware('auth');

?>