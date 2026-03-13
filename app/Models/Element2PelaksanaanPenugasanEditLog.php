<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PelaksanaanPenugasanEditLog extends Model
{
    protected $table = 'element2_pelaksanaan_penugasan_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
