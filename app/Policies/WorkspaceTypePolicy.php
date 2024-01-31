<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkspaceType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspaceTypePolicy
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
     * @param  \App\Models\WorkspaceType  $workspaceType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, WorkspaceType $workspaceType)
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
     * @param  \App\Models\WorkspaceType  $workspaceType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, WorkspaceType $workspaceType)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WorkspaceType  $workspaceType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, WorkspaceType $workspaceType)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WorkspaceType  $workspaceType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, WorkspaceType $workspaceType)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WorkspaceType  $workspaceType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, WorkspaceType $workspaceType)
    {
        return true;
    }

    public function replicate(User $user, WorkspaceType $workspaceType)
    {
        return false;
    }
}
