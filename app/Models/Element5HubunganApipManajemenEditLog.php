<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element5HubunganApipManajemenEditLog extends Model
{
    protected $table = 'element5_hubungan_apip_manajemen_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
