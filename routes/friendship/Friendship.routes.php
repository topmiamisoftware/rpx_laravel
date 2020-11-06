<?php

    /* Friendship Controllers */

    Route::post('friendship/add_friend', 'FriendshipsController@addFriend')->middleware('auth');
    Route::post('friendship/block', 'FriendshipsController@block')->middleware('auth');
    Route::post('friendship/report', 'FriendshipsController@report')->middleware('auth');
    
    Route::put('friendship/accept', 'FriendshipsController@acceptRequest')->middleware('auth');

    Route::get('friendship/show_friends', 'FriendshipsController@showFriends')->middleware('auth');
    Route::get('friendship/show_pending', 'FriendshipsController@showPending')->middleware('auth');
    Route::get('friendship/show_nearby', 'FriendshipsController@showNearby')->middleware('auth');
    Route::get('friendship/show_blocked', 'FriendshipsController@showBlocked')->middleware('auth');
    Route::get('friendship/check_relationship', 'FriendshipsController@checkRelationship')->middleware('auth');
    
    Route::delete('friendship/unfriend', 'FriendshipsController@unfriend')->middleware('auth');
    Route::delete('friendship/unblock', 'FriendshipsController@unblock')->middleware('auth');
    Route::delete('friendship/decline', 'FriendshipsController@declineRequest')->middleware('auth');
    Route::delete('friendship/cancel_request', 'FriendshipsController@cancelRequest')->middleware('auth');
