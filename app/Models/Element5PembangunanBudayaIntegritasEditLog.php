<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element5PembangunanBudayaIntegritasEditLog extends Model
{
    protected $table = 'element5_pembangunan_budaya_integritas_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
