<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementProgressArchive extends Model
{
    protected $table = 'element_progress_archives';

    protected $fillable = [
        'budget_year',
        'snapshot',
        'total_rows',
        'archived_by',
        'loaded_by',
        'last_loaded_at',
    ];

    protected $casts = [
        'budget_year' => 'integer',
        'snapshot' => 'array',
        'total_rows' => 'integer',
        'last_loaded_at' => 'datetime',
    ];
}

