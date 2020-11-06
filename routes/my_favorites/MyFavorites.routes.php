<?php

    Route::post('my-favorites', 'MyFavoritesController@getFavorites')->middleware('auth');

    Route::post('my-favorites/save-favorite', 'MyFavoritesController@saveFavorite')->middleware('auth');

    Route::delete('my-favorites/remove-favorite', 'MyFavoritesController@removeFavorite')->middleware('auth');

    Route::post('my-favorites/is-a-favorite', 'MyFavoritesController@isAFavorite')->middleware('auth');