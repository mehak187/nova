<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoorOpening;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoorOpeningPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DoorOpening  $doorOpening
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DoorOpening $doorOpening)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DoorOpening  $doorOpening
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DoorOpening $doorOpening)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DoorOpening  $doorOpening
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DoorOpening $doorOpening)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DoorOpening  $doorOpening
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, DoorOpening $doorOpening)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DoorOpening  $doorOpening
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, DoorOpening $doorOpening)
    {
        return true;
    }

    public function replicate(User $user, DoorOpening $doorOpening)
    {
        return false;
    }
}
