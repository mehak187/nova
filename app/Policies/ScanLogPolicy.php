<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ScanLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScanLogPolicy
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
     * @param  \App\Models\ScanLog  $scan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ScanLog $scan)
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
     * @param  \App\Models\ScanLog  $scan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ScanLog $scan)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ScanLog  $scan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ScanLog $scan)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ScanLog  $scan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ScanLog $scan)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ScanLog  $scan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ScanLog $scan)
    {
        return true;
    }

    public function replicate(User $user, ScanLog $scan)
    {
        return false;
    }
}
