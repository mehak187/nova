<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningTime extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function getTimeFromAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getTimeToAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getDayNameFrAttribute()
    {
        return [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ][$this->day_name];
    }
}
