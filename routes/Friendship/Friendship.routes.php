<?php

use App\Http\Controllers\Friendship\FriendshipsController;

use Illuminate\Support\Facades\Route;



Route::post('add-friend',           [FriendshipsController::class, 'addFriend'])->middleware('auth');

Route::post('block',                [FriendshipsController::class, 'block'])->middleware('auth');

Route::post('report',               [FriendshipsController::class, 'report'])->middleware('auth');

Route::put('accept',                [FriendshipsController::class, 'acceptRequest'])->middleware('auth');

Route::post('show-friends',         [FriendshipsController::class, 'showFriends'])->middleware('auth');

Route::post('show-pending',         [FriendshipsController::class, 'showPending'])->middleware('auth');

Route::post('show-nearby',          [FriendshipsController::class, 'showNearby'])->middleware('auth');

Route::post('show-blocked',         [FriendshipsController::class, 'showBlocked'])->middleware('auth');

Route::post('check-relationship',   [FriendshipsController::class, 'checkRelationship'])->middleware('auth');

Route::delete('unfriend',           [FriendshipsController::class, 'unfriend'])->middleware('auth');

Route::delete('unblock',            [FriendshipsController::class, 'unblock'])->middleware('auth');

Route::delete('decline',            [FriendshipsController::class, 'declineRequest'])->middleware('auth');

Route::delete('cancel-request',     [FriendshipsController::class, 'cancelRequest'])->middleware('auth');
