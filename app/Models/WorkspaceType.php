<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkspaceType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }
}
