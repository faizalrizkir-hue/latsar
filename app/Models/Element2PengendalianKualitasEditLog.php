<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PengendalianKualitasEditLog extends Model
{
    protected $table = 'element2_pengendalian_kualitas_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
