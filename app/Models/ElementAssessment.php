<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementAssessment extends Model
{
    protected $fillable = [
        'subtopic_slug',
        'subtopic_title',
        'scores',
        'weighted_total',
        'level',
        'predikat',
        'notes',
        'submitted_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'scores' => 'array',
        'weighted_total' => 'float',
        'verified_at' => 'datetime',
    ];
}
