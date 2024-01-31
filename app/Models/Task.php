<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->creator_id = $task->creator_id ?? auth('web')->id() ?? null;
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
