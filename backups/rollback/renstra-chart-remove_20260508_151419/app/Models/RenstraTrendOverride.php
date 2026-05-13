<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenstraTrendOverride extends Model
{
    protected $table = 'renstra_trend_overrides';

    protected $fillable = [
        'year',
        'hasil_score',
        'target_score',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'hasil_score' => 'float',
        'target_score' => 'float',
    ];
}

