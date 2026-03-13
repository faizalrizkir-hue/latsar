<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element3PelaporanManajemenKldEditLog extends Model
{
    protected $table = 'element3_pelaporan_manajemen_kld_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
