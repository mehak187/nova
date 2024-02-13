<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class urls extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'contact',
        'services',
        'prices',
        'descriptions',
        'conditions',
        'assistant_id'
    ];
}
