<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element1KegiatanAsuransEditLog extends Model
{
    protected $table = 'element1_kegiatan_asurans_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
