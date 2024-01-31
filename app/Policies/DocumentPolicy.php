<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Document $document)
    {
        return false;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Document $document)
    {
        return true;
    }

    public function delete(User $user, Document $document)
    {
        return true;
    }

    public function replicate(User $user, Document $document)
    {
        return false;
    }
}
