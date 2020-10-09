<?php

    /* Friendship Controllers */

    Route::post('friendship/add_friend', 'FriendshipsController@store')->middleware('auth');
    Route::post('friendship/block', 'FriendshipsController@block')->middleware('auth');
    Route::post('friendship/unblock', 'FriendshipsController@unblock')->middleware('auth');
    Route::post('friendship/report', 'FriendshipsController@report')->middleware('auth');

    Route::put('friendship/accept', 'FriendshipsController@accept')->middleware('auth');
    Route::put('friendship/decline', 'FriendshipsController@decline')->middleware('auth');

    Route::get('friendship/show_friends', 'FriendshipsController@showFriends')->middleware('auth');
    Route::get('friendship/show_pending', 'FriendshipsController@showPending')->middleware('auth');
    Route::get('friendship/show_nearby', 'FriendshipsController@showNearby')->middleware('auth');
    Route::get('friendship/show_blocked', 'FriendshipsController@showBlocked')->middleware('auth');

    Route::delete('friendship/unfriend', 'FriendshipsController@unfriend')->middleware('auth');

?>