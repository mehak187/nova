<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docs extends Model
{
    use HasFactory;
    protected $fillable = [
        'file',
        'assistant_id'
    ];
}
