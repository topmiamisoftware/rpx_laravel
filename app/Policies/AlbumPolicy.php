<?php

namespace App\Policies;

use Auth;

use App\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    public function viewAlbum(User $user = null, Album $album)
    {
        //check if private
        $album = Album::select('id', 'user_id', 'privacy')
        ->where('id', $album->id)
        ->first();

        if($album->privacy == true){

            //check if users are friends or if user is viewing their own media
            if(Auth::check()){

                //user is logged in, now we have to check for existing friendship.

                if($user->id == $album->user_id) return Response::allow();

                $user_friendship = $user->frienships()
                ->where('user_id', $user->id)
                ->where('peer_id', $album->user_id)
                ->get();

                $users_are_friends = false;

                if(count($user_friendship) == 1) $users_are_friends = true;

                $users_are_friends === true 
                    ? $view_right_granted = true
                    : $view_right_granted = false;

            } else {
                //user is not logged in, we cannot display this private album.
                $view_right_granted = false;
            }

        } else {
            //album is public, we can display it to anyone
            $view_right_granted = true;
        }

        return $view_right_granted === true 
                    ? Response::allow() 
                    : Response::deny('private_content');

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function update(User $user, Album $album)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function delete(User $user, Album $album)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function restore(User $user, Album $album)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function forceDelete(User $user, Album $album)
    {
        //
    }
}
