<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PerencanaanPenugasanEditLog extends Model
{
    protected $table = 'element2_perencanaan_penugasan_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
