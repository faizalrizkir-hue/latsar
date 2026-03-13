<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element4ManajemenKinerjaEditLog extends Model
{
    protected $table = 'element4_manajemen_kinerja_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
