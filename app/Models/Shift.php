<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_reservation' => 'boolean',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
        'paid_at',
    ];

    public $appends = [
        'date_key',
        'is_cancellable',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }

    public function scopeRunning($query)
    {
        $query->where('status', 'running');
    }

    public function getDurationInMinutesAttribute()
    {
        if (empty($this->ended_at)) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->ended_at);
    }

    public function start()
    {
        $this->update([
            'status' => 'running',
        ]);
    }

    public function getDateKeyAttribute()
    {
        return $this->started_at->format('H\hi');
    }

    public function getDisablesAttribute()
    {
        $start = $this->started_at->clone();

        $end = $this->ended_at
            ? $this->ended_at->clone()->subMinute()
            : now();

        $disabled = [];

        while ($start->isBefore($end)) {
            $disabled[] = $start->format('H\hi');

            $start->addMinutes(15);
        }

        return $disabled;
    }

    public function getIsCancellableAttribute()
    {
        return $this->started_at->diffInDays(now()) >= 3;
    }

    public function getIsPaidAttribute()
    {
        return !empty($this->paid_at);
    }
}
