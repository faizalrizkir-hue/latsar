<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element3PerencanaanPengawasanEditLog extends Model
{
    protected $table = 'element3_perencanaan_pengawasan_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
