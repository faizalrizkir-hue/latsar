<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element4PerencanaanSdmApipEditLog extends Model
{
    protected $table = 'element4_perencanaan_sdm_apip_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
