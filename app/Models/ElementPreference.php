<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementPreference extends Model
{
    protected $table = 'element_preferences';

    protected $fillable = [
        'payload',
        'updated_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
