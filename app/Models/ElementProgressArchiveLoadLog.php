<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementProgressArchiveLoadLog extends Model
{
    protected $table = 'element_progress_archive_load_logs';

    protected $fillable = [
        'archive_id',
        'budget_year',
        'restored_tables',
        'restored_total',
        'restored_by_table',
        'loaded_by',
    ];

    protected $casts = [
        'archive_id' => 'integer',
        'budget_year' => 'integer',
        'restored_tables' => 'integer',
        'restored_total' => 'integer',
        'restored_by_table' => 'array',
    ];
}
