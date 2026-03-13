<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element4MekanismePendanaanEditLog extends Model
{
    protected $table = 'element4_mekanisme_pendanaan_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
